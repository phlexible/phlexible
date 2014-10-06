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
     * @param ElementtypeService $elementtypeService
     */
    public function __construct(ElementtypeService $elementtypeService)
    {
        $this->elementtypeService = $elementtypeService;
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
            foreach ($this->elementtypeService->findAllowedParents($elementtype) as $viabilityElementtype) {
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