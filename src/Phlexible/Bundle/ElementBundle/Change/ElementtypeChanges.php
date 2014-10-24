<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Change;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;

/**
 * Elementtype changes
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeChanges
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
     * @var Synchronizer
     */
    private $synchronizer;

    /**
     * @param ElementtypeService $elementtypeService
     * @param ElementService     $elementService
     * @param Synchronizer       $synchronizer
     */
    public function __construct(
        ElementtypeService $elementtypeService,
        ElementService $elementService,
        Synchronizer $synchronizer)
    {
        $this->elementtypeService = $elementtypeService;
        $this->elementService = $elementService;
        $this->synchronizer = $synchronizer;
    }

    /**
     * @return Change[]
     */
    public function changes()
    {
        $changes = array();

        foreach ($this->elementtypeService->findAllElementtypes() as $elementtype) {
            $outdatedElementSources = $this->elementService->findOutdatedElementSources($elementtype);
            $changes[] = new Change($elementtype, $outdatedElementSources);
        }

        return $changes;
    }

    /**
     * @param Change $change
     * @param bool   $viaQueue
     */
    public function commit(Change $change, $viaQueue = false)
    {
        $this->synchronizer->synchronize($change);
    }
}
