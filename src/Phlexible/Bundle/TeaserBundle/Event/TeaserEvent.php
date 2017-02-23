<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Event;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Symfony\Component\EventDispatcher\Event;

/**
 * Teaser event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserEvent extends Event
{
    /**
     * @var Teaser
     */
    private $teaser;

    /**
     * @param Teaser $teaser
     */
    public function __construct(Teaser $teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * @return Teaser
     */
    public function getTeaser()
    {
        return $this->teaser;
    }
}
