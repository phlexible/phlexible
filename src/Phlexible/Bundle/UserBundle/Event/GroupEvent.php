<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Event;

use Phlexible\Bundle\UserBundle\Entity\Group;
use Symfony\Component\EventDispatcher\Event;

/**
 * Group event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GroupEvent extends Event
{
    /**
     * @var Group
     */
    private $group;

    /**
     * @param Group $group
     */
    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }
}
