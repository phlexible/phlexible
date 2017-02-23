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

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Apply successor event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ApplySuccessorEvent extends Event
{
    /**
     * @var UserInterface
     */
    private $fromUser;

    /**
     * @var UserInterface
     */
    private $toUser;

    /**
     * @param UserInterface $fromUser
     * @param UserInterface $toUser
     */
    public function __construct(UserInterface $fromUser, UserInterface $toUser)
    {
        $this->fromUser = $fromUser;
        $this->toUser = $toUser;
    }

    /**
     * @return UserInterface
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * @return UserInterface
     */
    public function getToUser()
    {
        return $this->toUser;
    }
}
