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
use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;

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
     * @param ElementtypeService            $elementtypeService
     * @param ElementService                $elementService
     * @param ElementSourceManagerInterface $elementSourceManager
     */
    public function __construct(
        ElementtypeService $elementtypeService,
        ElementService $elementService,
        ElementSourceManagerInterface $elementSourceManager)
    {
        $this->elementtypeService = $elementtypeService;
        $this->elementService = $elementService;
        $this->elementSourceManager = $elementSourceManager;
    }

    /**
     * @return Change[]
     */
    public function check()
    {
        $changes = [];

        foreach ($this->elementtypeService->findAllElementtypes() as $elementtype) {
            $needImport = true;
            if ($this->elementSourceManager->findByElementtype($elementtype)) {
                $needImport = false;
            }
            $outdatedElementSources = $this->elementService->findOutdatedElementSources($elementtype);
            $changes[] = new Change($elementtype, $needImport, $outdatedElementSources);
        }

        return $changes;
    }
}
