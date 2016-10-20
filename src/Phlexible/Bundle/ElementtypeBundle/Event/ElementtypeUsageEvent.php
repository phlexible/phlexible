<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
    private $usage = [];

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
