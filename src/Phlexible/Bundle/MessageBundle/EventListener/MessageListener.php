<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
        return array(
            MessageEvents::MESSAGE => 'onMessage',
            KernelEvents::TERMINATE => 'onTerminate',
            ConsoleEvents::TERMINATE => 'onTerminate',
        );
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
