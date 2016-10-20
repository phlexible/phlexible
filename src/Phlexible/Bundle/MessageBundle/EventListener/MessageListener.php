<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\EventListener;

use Phlexible\Bundle\MessageBundle\Event\MessageEvent;
use Phlexible\Bundle\MessageBundle\Handler\HandlerCollection;
use Phlexible\Bundle\MessageBundle\MessageEvents;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Message listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageListener implements EventSubscriberInterface
{
    /**
     * @var HandlerCollection
     */
    private $messageHandlers;

    /**
     * @param HandlerCollection       $messageHandlers
     */
    public function __construct(HandlerCollection $messageHandlers)
    {
        $this->messageHandlers = $messageHandlers;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            MessageEvents::MESSAGE => 'onMessage',
            KernelEvents::TERMINATE => 'onTerminate',
            ConsoleEvents::TERMINATE => 'onTerminate',
        ];
    }

    /**
     * @param MessageEvent $event
     */
    public function onMessage(MessageEvent $event)
    {
        $message = $event->getMessage();

        foreach ($this->messageHandlers as $messageHandler) {
            $messageHandler->handle($message);
        }
    }

    /**
     * On terminate
     */
    public function onTerminate()
    {
        foreach ($this->messageHandlers as $messageHandler) {
            $messageHandler->close();
        }
    }
}
