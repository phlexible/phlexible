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
     * @param ElementtypeService $elementtypeService
     * @param ElementService              $elementService
     */
    public function __construct(ElementtypeService $elementtypeService,
                                ElementService $elementService)
    {
        $this->elementtypeService = $elementtypeService;
        $this->elementService = $elementService;
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
                $change = new Change($elementVersion, $elementtype, $elementVersion->getElementtypeVersion());
                $changes[] = $change;
            }
        }

        return $changes;
    }

    /**
     * @param bool $viaQueue
     */
    public function commit($viaQueue = false)
    {
        $changes = $this->changes();

        foreach ($changes as $change) {

        }
    }
}
