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

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Symfony\Component\EventDispatcher\Event;

/**
 * Element version event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementVersionEvent extends Event
{
    /**
     * @var ElementVersion
     */
    private $elementVersion;

    /**
     * @param ElementVersion $elementVersion
     */
    public function __construct(ElementVersion $elementVersion)
    {
        $this->elementVersion = $elementVersion;
    }

    /**
     * @return ElementVersion
     */
    public function getElementVersion()
    {
        return $this->elementVersion;
    }
}
