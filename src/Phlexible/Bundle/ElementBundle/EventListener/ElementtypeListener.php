<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\ElementBundle\ElementService;
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
     * @var array
     */
    private $languages;

    /**
     * @param ElementService $elementService
     * @param string         $languages
     */
    public function __construct(ElementService $elementService, $languages)
    {
        $this->elementService = $elementService;
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

            $elementVersion = clone $latestElementVersion;
            $elementVersion
                ->setId(null)
                ->setElementtypeVersion($elementtypeVersion->getVersion())
                ->setVersion($elementVersion->getVersion() + 1)
                ->setCreatedAt(new \DateTime())
                ->setCreateUserId($elementtypeVersion->getCreateUserId());

            $element
                ->setLatestVersion($elementVersion->getVersion());

            $elementStructures = array();
            foreach ($this->languages as $language) {
                $latestElementStructure = $this->elementService->findElementStructure($latestElementVersion, $language);

                $elementStructures[$language] = $this->iterateStructure($latestElementStructure, $elementVersion);
            }

            $this->elementService->updateElement($element, false);
            $this->elementService->updateElementVersion($elementVersion, false);
            foreach ($elementStructures as $elementStructure) {
                $this->elementService->updateElementStructure($elementStructure);
            }
        }

        // TODO: meta, titles
    }

    /**
     * @param ElementStructure $structure
     * @param ElementVersion   $elementVersion
     *
     * @return ElementStructure
     */
    private function iterateStructure(ElementStructure $structure, ElementVersion $elementVersion)
    {
        $elementStructure = new ElementStructure();
        $elementStructure
            ->setElementVersion($elementVersion);

        foreach ($structure->getValues() as $value) {
            $elementStructure->setValue($value);
        }

        foreach ($structure->getStructures() as $childStructure) {
            $elementStructure->addStructure($this->iterateStructure($childStructure, $elementVersion));
        }

        return $elementStructure;
    }
}
