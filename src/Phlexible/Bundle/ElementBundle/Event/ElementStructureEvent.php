<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Event;

use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Symfony\Component\EventDispatcher\Event;

/**
 * Element structure event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementStructureEvent extends Event
{
    /**
     * @var ElementStructure
     */
    private $elementStructure;

    /**
     * @param ElementStructure $elementStructure
     */
    public function __construct(ElementStructure $elementStructure)
    {
        $this->elementStructure = $elementStructure;
    }

    /**
     * @return ElementStructure
     */
    public function getElementStructure()
    {
        return $this->elementStructure;
    }
}
