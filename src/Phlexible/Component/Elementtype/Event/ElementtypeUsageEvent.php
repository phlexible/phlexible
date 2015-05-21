<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\Event;

use Phlexible\Component\Elementtype\Model\Elementtype;
use Phlexible\Component\Elementtype\Usage\Usage;

/**
 * Elementtype event
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class ElementtypeUsageEvent extends ElementtypeEvent
{
    /**
     * @var \Phlexible\Component\Elementtype\Usage\Usage[]
     */
    private $usage = [];

    /**
     * @param \Phlexible\Component\Elementtype\Model\Elementtype $elementtype
     */
    public function __construct(Elementtype $elementtype)
    {
        parent::__construct($elementtype);
    }

    /**
     * @param \Phlexible\Component\Elementtype\Usage\Usage $usage
     */
    public function addUsage(Usage $usage)
    {
        $this->usage[$usage->getId()] = $usage;
    }

    /**
     * @return \Phlexible\Component\Elementtype\Usage\Usage[]
     */
    public function getUsage()
    {
        return $this->usage;
    }
}
