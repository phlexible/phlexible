<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Message;

use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Event\MessageEvent;
use Phlexible\Bundle\MessageBundle\MessageEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Message poster
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessagePoster
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Message $message
     */
    public function post(Message $message)
    {
        $event = new MessageEvent($message);
        $this->dispatcher->dispatch(MessageEvents::MESSAGE, $event);
    }
}
