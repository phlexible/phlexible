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
use Phlexible\Bundle\ElementBundle\ElementStructure\LinkExtractor\LinkExtractor;
use Phlexible\Bundle\ElementBundle\Entity\ElementLink;
use Phlexible\Bundle\ElementBundle\Entity\ElementStructure as StructureEntity;
use Phlexible\Bundle\ElementBundle\Entity\ElementStructureValue as ValueEntity;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Field\FieldRegistry;
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
     * @var FieldRegistry
     */
    private $fieldRegistry;

    /**
     * @var LinkExtractor
     */
    private $linkExtractor;

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
     * @param FieldRegistry                 $fieldRegistry
     * @param LinkExtractor                 $linkExtractor
     * @param EventDispatcherInterface      $dispatcher
     * @param MessagePoster                 $messagePoster
     */
    public function __construct(
        EntityManager $entityManager,
        ElementStructureLoader $elementStructureLoader,
        ElementStructureSequence $elementStructureSequence,
        ElementStructureValueSequence $elementStructureValueSequence,
        FieldRegistry $fieldRegistry,
        LinkExtractor $linkExtractor,
        EventDispatcherInterface $dispatcher,
        MessagePoster $messagePoster)
    {
        $this->entityManager = $entityManager;
        $this->elementStructureLoader = $elementStructureLoader;
        $this->elementStructureSequence = $elementStructureSequence;
        $this->elementStructureValueSequence = $elementStructureValueSequence;
        $this->fieldRegistry = $fieldRegistry;
        $this->linkExtractor = $linkExtractor;
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
    public function updateElementStructure(ElementStructure $elementStructure, $onlyValues = false, $flush = true)
    {
        $conn = $this->entityManager->getConnection();

        $this->insertStructure($elementStructure, $conn, $onlyValues, true);

        $this->insertLinks($elementStructure);

        if ($flush) {
            $this->entityManager->flush();
        }
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
            $structureEntity = new StructureEntity();
            $structureEntity
                ->setDataId($elementStructure->getId())
                ->setElement($elementStructure->getElementVersion()->getElement())
                ->setVersion($elementStructure->getElementVersion()->getVersion())
                ->setDsId($elementStructure->getDsId())
                ->setType('group')
                ->setName($elementStructure->getName())
                ->setRepeatableId($elementStructure->getRepeatableId() ?: null)
                ->setRepeatableDsId($elementStructure->getRepeatableDsId() ?: null)
                ->setSort(0);

            $this->entityManager->persist($structureEntity);
            /*
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
            */
        }

        foreach ($elementStructure->getValues() as $elementStructureValue) {
            if ($elementStructureValue->getValue()) {
                $value = $elementStructureValue->getValue();
                $field = $this->fieldRegistry->getField($elementStructureValue->getType());
                $value = trim($field->toRaw($value));

                $valueEntity = new ValueEntity();
                $valueEntity
                    ->setDataId($elementStructureValue->getId())
                    ->setElement($elementStructure->getElementVersion()->getElement())
                    ->setVersion($elementStructure->getElementVersion()->getVersion())
                    ->setLanguage($elementStructureValue->getLanguage())
                    ->setDsId($elementStructureValue->getDsId())
                    ->setType($elementStructureValue->getType())
                    ->setName($elementStructureValue->getName())
                    ->setRepeatableId($elementStructure->getId() ?: null)
                    ->setRepeatableDsId($elementStructure->getDsId() ?: null)
                    ->setContent($value)
                    ->setOptions(!empty($elementStructureValue->getOptions()) ? $elementStructureValue->getOptions() : null);

                $this->entityManager->persist($valueEntity);
                /*
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
                        'content'          => $value,
                        'options'          => !empty($elementStructureValue->getOptions()) ? $elementStructureValue->getOptions() : null,
                    )
                );
                */
            }
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
        $links = $this->extractLinks($elementStructure);

        foreach ($links as $link) {
            $link->setElementVersion($elementStructure->getElementVersion());

            $this->entityManager->persist($link);
        }
    }

    /**
     * @param ElementStructure $elementStructure
     *
     * @return ElementLink[]
     */
    private function extractLinks(ElementStructure $elementStructure)
    {
        $links = array();

        foreach ($elementStructure->getValues() as $elementStructureValue) {
            $links = array_merge($links, $this->linkExtractor->extract($elementStructureValue));
        }

        foreach ($elementStructure->getStructures() as $childStructure) {
            $links = array_merge($links, $this->extractLinks($childStructure));
        }

        return $links;
    }
}
