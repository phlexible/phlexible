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
            $elementVersions = $this->elementService->findOutdatedElementVersions($elementtype);

            foreach ($elementVersions as $elementVersion) {
                $index = "{$elementtype->getId()}__{$elementVersion->getElementtypeVersion()}";
                $changes[$index] = new Change($elementtype, $elementVersion->getElementtypeVersion());
                $changes[$index]->addElementVersion($elementVersion);
            }
        }

        return $changes;
    }

    /**
     * @param bool $viaQueue
     */
    public function commit($viaQueue = false)
    {
        foreach ($this->changes() as $change) {
            $this->synchronizer->synchronize($change);
        }
    }
}
