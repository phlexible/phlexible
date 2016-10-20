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

use Phlexible\Bundle\ElementBundle\Entity\Element;
use Symfony\Component\EventDispatcher\Event;

/**
 * Element event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementEvent extends Event
{
    /**
     * @var Element
     */
    private $element;

    /**
     * @param Element $element
     */
    public function __construct(Element $element)
    {
        $this->element = $element;
    }

    /**
     * @return Element
     */
    public function getElement()
    {
        return $this->element;
    }
}
