<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Apply successor event
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
        $this->toUser   = $toUser;
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