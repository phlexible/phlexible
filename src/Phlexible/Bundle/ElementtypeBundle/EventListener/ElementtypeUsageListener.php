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
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @var ViabilityManagerInterface
     */
    private $viabilityManager;

    /**
     * @param ElementtypeService        $elementtypeService
     * @param ViabilityManagerInterface $viabilityManager
     */
    public function __construct(ElementtypeService $elementtypeService, ViabilityManagerInterface $viabilityManager)
    {
        $this->elementtypeService = $elementtypeService;
        $this->viabilityManager = $viabilityManager;
    }

    /**
     * @param ElementtypeUsageEvent $event
     */
    public function onElementtypeUsage(ElementtypeUsageEvent $event)
    {
        $elementtype = $event->getElementtype();

        $elementtypes = $this->elementtypeService->findElementtypesUsingReferenceElementtype($elementtype);
        foreach ($elementtypes as $elementtype) {
            $event->addUsage(
                new Usage(
                    $elementtype->getType() . ' elementtype',
                    'reference',
                    $elementtype->getId(),
                    $elementtype->getTitle(),
                    $elementtype->getRevision()
                )
            );
        }

        if ($elementtype->getType() === 'layout') {
            foreach ($this->viabilityManager->findAllowedParents($elementtype) as $viabilityElementtype) {
                $event->addUsage(
                    new Usage(
                        $viabilityElementtype->getType() . ' elementtype',
                        'layout area',
                        $viabilityElementtype->getId(),
                        $viabilityElementtype->getTitle(),
                        $viabilityElementtype->getRevision()
                    )
                );
            }
        }
    }
}