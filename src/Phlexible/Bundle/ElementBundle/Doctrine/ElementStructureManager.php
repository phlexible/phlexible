<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementBundle\Entity\ElementLink;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureManagerInterface;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element structure manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementStructureManager implements ElementStructureManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ElementStructureLoader
     */
    private $elementStructureLoader;

    /**
     * @var ElementStructureSequence
     */
    private $elementStructureSequence;

    /**
     * @var ElementStructureValueSequence
     */
    private $elementStructureValueSequence;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var MessagePoster
     */
    private $messagePoster;

    /**
     * @param EntityManager                 $entityManager
     * @param ElementStructureLoader        $elementStructureLoader
     * @param ElementStructureSequence      $elementStructureSequence
     * @param ElementStructureValueSequence $elementStructureValueSequence
     * @param EventDispatcherInterface      $dispatcher
     * @param MessagePoster                 $messagePoster
     */
    public function __construct(
        EntityManager $entityManager,
        ElementStructureLoader $elementStructureLoader,
        ElementStructureSequence $elementStructureSequence,
        ElementStructureValueSequence $elementStructureValueSequence,
        EventDispatcherInterface $dispatcher,
        MessagePoster $messagePoster)
    {
        $this->entityManager = $entityManager;
        $this->elementStructureLoader = $elementStructureLoader;
        $this->elementStructureSequence = $elementStructureSequence;
        $this->elementStructureValueSequence = $elementStructureValueSequence;
        $this->dispatcher = $dispatcher;
        $this->messagePoster = $messagePoster;
    }

    /**
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return ElementStructure
     */
    public function find(ElementVersion $elementVersion, $language)
    {
        return $this->elementStructureLoader->load($elementVersion, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function updateElementStructure(ElementStructure $elementStructure, $onlyValues = false)
    {
        $conn = $this->entityManager->getConnection();

        $this->insertStructure($elementStructure, $conn, $onlyValues, true);

        $this->insertLinks($elementStructure);
    }

    /**
     * {@inheritdoc}
     */
    public function getNextStructureId()
    {
        return $this->elementStructureSequence->next();
    }

    /**
     * {@inheritdoc}
     */
    public function getNextStructureValueId()
    {
        return $this->elementStructureValueSequence->next();
    }

    /**
     * @param ElementStructure $elementStructure
     * @param Connection       $conn
     * @param bool             $onlyValues
     * @param bool             $isRoot
     */
    private function insertStructure(ElementStructure $elementStructure, Connection $conn, $onlyValues, $isRoot = false)
    {
        if (!$isRoot && !$onlyValues) {
            $conn->insert(
                'element_structure',
                array(
                    'data_id'          => $elementStructure->getId(),
                    'eid'              => $elementStructure->getElementVersion()->getElement()->getEid(),
                    'version'          => $elementStructure->getElementVersion()->getVersion(),
                    'ds_id'            => $elementStructure->getDsId(),
                    'type'             => 'group',//$elementStructure->getType(),
                    'name'             => $elementStructure->getName(),
                    'cnt'              => 0,
                    'repeatable_id'    => $elementStructure->getRepeatableId() ?: null,
                    'repeatable_ds_id' => $elementStructure->getRepeatableDsId() ?: null,
                    'sort'             => 0,
                )
            );
        }

        foreach ($elementStructure->getValues() as $elementStructureValue) {
            if (strlen(trim($elementStructureValue->getValue()))) {
                $conn->insert(
                    'element_structure_value',
                    array(
                        'data_id'          => $elementStructureValue->getId(),
                        'eid'              => $elementStructure->getElementVersion()->getElement()->getEid(),
                        'version'          => $elementStructure->getElementVersion()->getVersion(),
                        'language'         => $elementStructureValue->getLanguage(),
                        'ds_id'            => $elementStructureValue->getDsId(),
                        'type'             => $elementStructureValue->getType(),
                        'name'             => $elementStructureValue->getName(),
                        'repeatable_id'    => $elementStructure->getId() ?: null,
                        'repeatable_ds_id' => $elementStructure->getDsId() ?: null,
                        'content'          => trim($elementStructureValue->getValue()),
                        'options'          => !empty($elementStructureValue->getOptions()) ? $elementStructureValue->getOptions() : null,
                    )
                );}
        }

        foreach ($elementStructure->getStructures() as $childStructure) {
            $this->insertStructure($childStructure, $conn, $onlyValues);
        }
    }

    /**
     * @param ElementStructure $elementStructure
     */
    private function insertLinks(ElementStructure $elementStructure)
    {
        foreach ($elementStructure->getValues() as $elementStructureValue) {
            if (strlen(trim($elementStructureValue->getValue()))) {
                if ($elementStructureValue->getType() === 'link') {
                    $link = new ElementLink();
                    $link
                        ->setElementVersion($elementStructure->getElementVersion())
                        ->setLanguage($elementStructureValue->getLanguage())
                        ->setType('link')
                        ->setField($elementStructureValue->getName())
                        ->setTarget($elementStructureValue->getValue());
                } elseif ($elementStructureValue->getType() === 'image') {
                    $link = new ElementLink();
                    $link
                        ->setElementVersion($elementStructure->getElementVersion())
                        ->setLanguage($elementStructureValue->getLanguage())
                        ->setType('file')
                        ->setField($elementStructureValue->getName())
                        ->setTarget($elementStructureValue->getValue());
                }
            }
        }

        foreach ($elementStructure->getStructures() as $childStructure) {
            $this->insertLinks($childStructure);
        }
    }
}
