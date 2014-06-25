<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Successor;

use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Phlexible\Bundle\UserBundle\Entity\User;
use Phlexible\Bundle\UserBundle\Event\ApplySuccessorEvent;
use Phlexible\Bundle\UserBundle\UserEvents;
use Phlexible\Bundle\UserBundle\UsersMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Successor service
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SuccessorService
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var MessagePoster
     */
    private $messageService;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param MessagePoster           $messageService
     */
    public function __construct(EventDispatcherInterface $dispatcher, MessagePoster $messageService)
    {
        $this->dispatcher = $dispatcher;
        $this->messageService = $messageService;
    }

    /**
     * Set successor
     *
     * @param User $fromUser
     * @param User $toUser
     */
    public function set(User $fromUser, User $toUser)
    {
        $event = new ApplySuccessorEvent($fromUser, $toUser);
        $this->dispatcher->dispatch(UserEvents::APPLY_SUCCESSOR, $event);

        $message = UsersMessage::create(sprintf('Set "%s" as successor for "%s".', $toUser->getUsername(), $fromUser->getUsername()));
        $this->messageService->post($message);
    }
}