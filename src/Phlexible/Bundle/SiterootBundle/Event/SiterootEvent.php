<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\Event;

use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Symfony\Component\EventDispatcher\Event;

/**
 * Siteroot event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootEvent extends Event
{
    /**
     * @var Siteroot
     */
    private $siteroot;

    /**
     * @param Siteroot $siteroot
     */
    public function __construct(Siteroot $siteroot)
    {
        $this->siteroot = $siteroot;
    }

    /**
     * @return Siteroot
     */
    public function getSiteroot()
    {
        return $this->siteroot;
    }
}
