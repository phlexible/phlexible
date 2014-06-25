<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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