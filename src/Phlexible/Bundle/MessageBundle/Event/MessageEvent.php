<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
