<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\EventListener;

use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeUsageEvent;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Model\ViabilityManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Usage\Usage;

/**
 * Elementtype usage listeners
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeUsageListener
{
    /**
     * @var ElementtypeStructureManagerInterface
     */
    private $elementtypeStructureManager;

    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @param ElementtypeStructureManagerInterface $elementtypeStructureManager
     * @param ElementtypeService                   $elementtypeService
     */
    public function __construct(ElementtypeStructureManagerInterface $elementtypeStructureManager, ElementtypeService $elementtypeService)
    {
        $this->elementtypeStructureManager = $elementtypeStructureManager;
        $this->elementtypeService = $elementtypeService;
    }

    /**
     * @param ElementtypeUsageEvent $event
     */
    public function onElementtypeUsage(ElementtypeUsageEvent $event)
    {
        $elementtype = $event->getElementtype();

        $nodes = $this->elementtypeStructureManager->findNodesByReferenceElementtype($elementtype);
        foreach ($nodes as $node) {
            $event->addUsage(
                new Usage(
                    $node->getElementtype()->getType() . ' elementtype',
                    'reference',
                    $node->getElementtype()->getId(),
                    $node->getElementtype()->getTitle(),
                    $node->getElementtype()->getLatestVersion()
                )
            );
        }

        if ($elementtype->getType() === 'layout') {
            foreach ($this->elementtypeService->findAllowedParentIds($elementtype) as $viabilityId) {
                $viabilityElementtype = $this->elementtypeService->findElementtype($viabilityId);
                $event->addUsage(
                    new Usage(
                        $viabilityElementtype->getType() . ' elementtype',
                        'layout area',
                        $viabilityElementtype->getId(),
                        $viabilityElementtype->getTitle(),
                        $viabilityElementtype->getLatestVersion()
                    )
                );
            }
        }
    }
}