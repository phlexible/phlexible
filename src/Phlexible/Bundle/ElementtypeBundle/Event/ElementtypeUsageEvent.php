<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Event;

use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Usage\Usage;

/**
 * Elementtype event
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class ElementtypeUsageEvent extends ElementtypeEvent
{
    /**
     * @var Usage[]
     */
    private $usage = array();

    /**
     * @param Elementtype $elementtype
     */
    public function __construct(Elementtype $elementtype)
    {
        parent::__construct($elementtype);
    }

    /**
     * @param Usage $usage
     */
    public function addUsage(Usage $usage)
    {
        $this->usage[$usage->getId()] = $usage;
    }

    /**
     * @return Usage[]
     */
    public function getUsage()
    {
        return $this->usage;
    }
}