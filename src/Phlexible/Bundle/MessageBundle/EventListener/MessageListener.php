<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\EventListener;

use Phlexible\Bundle\MessageBundle\Event\MessageEvent;
use Phlexible\Bundle\MessageBundle\Handler\BufferHandler;
use Phlexible\Bundle\MessageBundle\Handler\HandlerCollection;
use Phlexible\Bundle\MessageBundle\Model\MessageManagerInterface;

/**
 * Message listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageListener
{
    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var HandlerCollection
     */
    private $messageHandlers;

    /**
     * @param MessageManagerInterface $messageManager
     * @param HandlerCollection       $messageHandlers
     */
    public function __construct(MessageManagerInterface $messageManager, HandlerCollection $messageHandlers)
    {
        $this->messageManager = $messageManager;
        $this->messageHandlers = $messageHandlers;
    }

    /**
     * @param MessageEvent $event
     */
    public function onMessage(MessageEvent $event)
    {
        $message = $event->getMessage();

        //$this->messageManager->updateMessage($message);

        foreach ($this->messageHandlers as $messageHandler) {
            $messageHandler->handle($message);
        }
    }

    public function onTerminate()
    {
        foreach ($this->messageHandlers as $messageHandler) {
            if ($messageHandler instanceof BufferHandler) {
                foreach ($messageHandler->getMessages() as $message) {
                    $this->messageManager->updateMessage($message);
                }

                return;
            }
        }
    }
}
