<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Handler;

use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Model\MessageManagerInterface;

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
    private $priorityNames;

    /**
     * @var array
     */
    private $typeNames;

    /**
     * @var Message[]
     */
    private $messages = array();

    /**
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(MessageManagerInterface $messageManager)
    {
        $this->priorityNames = $messageManager->getPriorityNames();
        $this->typeNames = $messageManager->getTypeNames();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Message $message)
    {
        $this->messages[] = array(
            'subject'      => $message->getSubject(),
            'body'         => $message->getBody(),
            'type'         => $message->getType(),
            'typeName'     => $this->typeNames[$message->getType()],
            'priority'     => $message->getPriority(),
            'priorityName' => $this->priorityNames[$message->getPriority()],
            'channel'      => $message->getChannel(),
            'resource'     => $message->getResource(),
            'user'         => $message->getUser(),
            'createdAt'    => $message->getCreatedAt()->format('Y-m-d H:i:s'),
        );
    }

    /**
     * @return Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
