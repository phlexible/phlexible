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
 * Handler interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface HandlerInterface
{
    /**
     * Handle the incoming message
     *
     * @param Message $message
     */
    public function handle(Message $message);
}