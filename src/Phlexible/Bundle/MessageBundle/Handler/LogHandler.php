<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Handler;

use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Model\MessageManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Log handler.
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
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Message $message)
    {
        $priority = $message->getPriority();

        $channel = $message->getChannel();
        $role = $message->getRole();
        $subject = $message->getSubject();
        $body = $message->getBody();

        // build message
        $msg = "Message ($priority)";

        if (!empty($channel)) {
            $msg .= ' in channel '.$channel;
        }

        if (!empty($role)) {
            $msg .= ' with role '.$role;
        }

        $msg .= ': '.$subject;

        // log message
        if ($message->getType() === Message::TYPE_ERROR) {
            if (!empty($body)) {
                $msg .= PHP_EOL.$body;
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
