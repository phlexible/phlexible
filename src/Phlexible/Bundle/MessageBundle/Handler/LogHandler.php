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
use Psr\Log\LoggerInterface;

/**
 * Log handler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LogHandler implements HandlerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @param LoggerInterface         $logger
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(LoggerInterface $logger, MessageManagerInterface $messageManager)
    {
        $this->logger = $logger;
        $this->messageManager = $messageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Message $message)
    {
        $priorities = $this->messageManager->getPriorityNames();
        $priority = $priorities[$message->getPriority()];

        $channel   = $message->getChannel();
        $resource  = $message->getResource();
        $subject   = $message->getSubject();
        $body      = $message->getBody();

        // build message
        $msg = "Message ($priority)";

        if (!empty($channel)) {
            $msg .= ' in channel ' . $channel;
        }

        if (!empty($resource)) {
            $msg .= ' with resource ' . $resource;
        }

        $msg .= ': ' . $subject;

        // log message
        if ($message->getType() === Message::TYPE_ERROR) {
            if (!empty($body)) {
                $msg .= PHP_EOL . $body;
            }

            $this->logger->error($msg);
        } else {
            $this->logger->debug($msg);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
    }
}
