<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Successor;

use FOS\UserBundle\Model\UserInterface;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Phlexible\Bundle\UserBundle\Event\ApplySuccessorEvent;
use Phlexible\Bundle\UserBundle\UserEvents;
use Phlexible\Bundle\UserBundle\UsersMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Successor service.
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
     * @param MessagePoster            $messageService
     */
    public function __construct(EventDispatcherInterface $dispatcher, MessagePoster $messageService)
    {
        $this->dispatcher = $dispatcher;
        $this->messageService = $messageService;
    }

    /**
     * Set successor.
     *
     * @param UserInterface $fromUser
     * @param UserInterface $toUser
     */
    public function set(UserInterface $fromUser, UserInterface $toUser)
    {
        $event = new ApplySuccessorEvent($fromUser, $toUser);
        $this->dispatcher->dispatch(UserEvents::APPLY_SUCCESSOR, $event);

        $message = UsersMessage::create(
            sprintf('Set "%s" as successor for "%s".', $toUser->getUsername(), $fromUser->getUsername())
        );
        $this->messageService->post($message);
    }
}
