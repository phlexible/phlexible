<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Event;

use Phlexible\Bundle\MessageBundle\Entity\Message;
use Symfony\Component\EventDispatcher\Event;

/**
 * Message event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageEvent extends Event
{
    /**
     * @var Message
     */
    private $message;

    /**
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Return mailserver
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
