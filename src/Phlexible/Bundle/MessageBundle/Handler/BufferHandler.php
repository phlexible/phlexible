<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Handler;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Buffer handler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BufferHandler implements HandlerInterface
{
    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var Message[]
     */
    private $messages = [];

    /**
     * @param HandlerInterface $handler
     */
    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Message $message)
    {
        $this->messages[] = $message;
    }

    /**
     * @return Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        foreach ($this->messages as $message) {
            $this->handler->handle($message);
        }
    }
}
