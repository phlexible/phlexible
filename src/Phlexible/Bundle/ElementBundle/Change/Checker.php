<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Change;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Component\Elementtype\ElementtypeService;
use Phlexible\Component\Elementtype\Usage\UsageManager;

/**
 * Elementtype change checker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Checker
{
    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var ElementSourceManagerInterface
     */
    private $elementSourceManager;

    /**
     * @var UsageManager
     */
    private $elementtypeUsageManager;

    /**
     * @param ElementtypeService            $elementtypeService
     * @param ElementService                $elementService
     * @param ElementSourceManagerInterface $elementSourceManager
     * @param UsageManager                  $elementtypeUsageManager
     */
    public function __construct(
        ElementtypeService $elementtypeService,
        ElementService $elementService,
        ElementSourceManagerInterface $elementSourceManager,
        UsageManager $elementtypeUsageManager
    )
    {
        $this->elementtypeService = $elementtypeService;
        $this->elementService = $elementService;
        $this->elementSourceManager = $elementSourceManager;
        $this->elementtypeUsageManager = $elementtypeUsageManager;
    }

    /**
     * @param bool $forceChange
     *
     * @return ChangeCollection
     */
    public function check($forceChange = false)
    {
        $changes = [];

        $allElementtypes = $this->elementtypeService->findAllElementtypes();

        $elementSources = array();
        foreach ($this->elementSourceManager->findBy() as $elementSource) {
            $elementSources[$elementSource->getElementtypeId()][] = $elementSource;
        }

        $referenceElementtypeIds = [];
        // handle references
        foreach ($allElementtypes as $elementtype) {
            if ($elementtype->getType() !== 'reference') {
                continue;
            }
            unset($elementSources[$elementtype->getId()]);
            $oldElementSources = $this->elementSourceManager->findByElementtype($elementtype);
            if (!$oldElementSources) {
                $changes[] = new AddChange($elementtype, true);
            } else {
                $usage = $this->elementtypeUsageManager->getUsage($elementtype);
                $outdatedElementSources = [];
                foreach ($oldElementSources as $oldElementSource) {
                    if ($forceChange ||$oldElementSource->getElementtypeRevision() < $elementtype->getRevision()) {
                        $outdatedElementSources[] = $oldElementSource;
                    }
                }
                if (count($outdatedElementSources)) {
                    $changes[] = new UpdateChange($elementtype, $usage, true, $outdatedElementSources);
                    $referenceElementtypeIds[$elementtype->getId()] = $elementtype->getRevision();
                }
            }
        }

        // handle non-references
        foreach ($allElementtypes as $elementtype) {
            if ($elementtype->getType() === 'reference') {
                continue;
            }
            unset($elementSources[$elementtype->getId()]);
            $oldElementSources = $this->elementSourceManager->findByElementtype($elementtype);
            if (!$oldElementSources) {
                $changes[] = new AddChange($elementtype, true);
            } else {
                $usage = $this->elementtypeUsageManager->getUsage($elementtype);
                $outdatedElementSources = [];
                $referenceIds = array_intersect_key(
                    array_flip($elementtype->getStructure()->getReferences()),
                    $referenceElementtypeIds
                );

                if (count($referenceIds)) {
                    foreach ($oldElementSources as $oldElementSource) {
                        $outdatedElementSources[] = $oldElementSource;
                    }
                    if (count($outdatedElementSources)) {
                        $changes[] = new ReferenceChange($elementtype, $usage, true, $outdatedElementSources);
                    }
                } else {
                    foreach ($oldElementSources as $oldElementSource) {
                        if ($forceChange || $oldElementSource->getElementtypeRevision() < $elementtype->getRevision()) {
                            $outdatedElementSources[] = $oldElementSource;
                        }
                    }
                    if (count($outdatedElementSources)) {
                        $changes[] = new UpdateChange($elementtype, $usage, true, $outdatedElementSources);
                    }
                }
            }
        }

        foreach ($elementSources as $elementtypeId => $removedElementSources) {
            $elementtype = $this->elementSourceManager->findElementtype($elementtypeId);
            $usage = $this->elementtypeUsageManager->getUsage($elementtype);
            $changes[] = new RemoveChange($elementtype, $usage, $removedElementSources);
        }

        return new ChangeCollection($changes);
    }
}
