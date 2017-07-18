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

use Phlexible\Bundle\ElementBundle\Entity\ElementLink;
use Symfony\Component\EventDispatcher\Event;

/**
 * Element link event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementLinkEvent extends Event
{
    /**
     * @var ElementLink
     */
    private $elementLink;

    /**
     * @param ElementLink $elementLink
     */
    public function __construct(ElementLink $elementLink)
    {
        $this->elementLink = $elementLink;
    }

    /**
     * @return ElementLink
     */
    public function getElementLink()
    {
        return $this->elementLink;
    }
}
