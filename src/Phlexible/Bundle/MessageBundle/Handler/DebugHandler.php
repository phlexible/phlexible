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
 * Debug handler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DebugHandler implements HandlerInterface
{
    /**
     * @var array
     */
    private $messages = [];

    /**
     * {@inheritdoc}
     */
    public function handle(Message $message)
    {
        $this->messages[] = [
            'subject'      => $message->getSubject(),
            'body'         => $message->getBody(),
            'type'         => $message->getType(),
            'typeName'     => $message->getType(),
            'priority'     => $message->getPriority(),
            'priorityName' => $message->getPriority(),
            'channel'      => $message->getChannel(),
            'role'         => $message->getRole(),
            'user'         => $message->getUser(),
            'createdAt'    => $message->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
