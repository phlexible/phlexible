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

use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
use Symfony\Component\EventDispatcher\Event;

/**
 * Element source event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementSourceEvent extends Event
{
    /**
     * @var ElementSource
     */
    private $elementSource;

    /**
     * @param ElementSource $elementSource
     */
    public function __construct(ElementSource $elementSource)
    {
        $this->elementSource = $elementSource;
    }

    /**
     * @return ElementSource
     */
    public function getElementSource()
    {
        return $this->elementSource;
    }
}
