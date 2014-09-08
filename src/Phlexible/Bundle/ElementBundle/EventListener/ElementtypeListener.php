<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\ElementVersion\FieldMapper;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeEvents;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeEvent;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeVersionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Elementtype listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeListener implements EventSubscriberInterface
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var FieldMapper
     */
    private $fieldMapper;

    /**
     * @var array
     */
    private $languages;

    /**
     * @param ElementService $elementService
     * @param FieldMapper    $fieldMapper
     * @param string         $languages
     */
    public function __construct(ElementService $elementService, FieldMapper $fieldMapper, $languages)
    {
        $this->elementService = $elementService;
        $this->fieldMapper = $fieldMapper;
        $this->languages = explode(',', $languages);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ElementtypeEvents::VERSION_CREATE => 'onElementtypeVersionCreate',
        );
    }

    /**
     * @param ElementtypeVersionEvent $event
     */
    public function onElementtypeVersionCreate(ElementtypeVersionEvent $event)
    {
        $elementtypeVersion = $event->getElementtypeVersion();

        $elements = $this->elementService->findElementsByElementtype($elementtypeVersion->getElementtype());

        foreach ($elements as $element) {
            $latestElementVersion = $this->elementService->findLatestElementVersion($element);

            $elementStructures = array();
            foreach ($this->languages as $language) {
                $latestElementStructure = $this->elementService->findElementStructure($latestElementVersion, $language);

                $elementStructures[$language] = $elementStructure = $this->iterateStructure($latestElementStructure);
            }

            $elementVersion = $this->elementService->createElementVersion(
                $element,
                $elementStructures,
                null,
                $elementtypeVersion->getCreateUserId()
            );
        }

        // TODO: meta, titles
    }

    /**
     * @param ElementStructure $structure
     *
     * @return ElementStructure
     */
    private function iterateStructure(ElementStructure $structure)
    {
        $elementStructure = new ElementStructure();
        $elementStructure
            ->setId($structure->getId())
            ->setDsId($structure->getDsId())
            ->setName($structure->getName())
            //->setParentId($structure->getParentId())
            //->setParentDsId($structure->getParentDsId())
            ->setParentName($structure->getParentName());
        ;

        foreach ($structure->getValues() as $value) {
            $elementStructure->setValue($value);
        }

        foreach ($structure->getStructures() as $childStructure) {
            $elementStructure->addStructure($this->iterateStructure($childStructure));
        }

        return $elementStructure;
    }
}
