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

/**
 * Handler interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface HandlerInterface
{
    /**
     * Will be called as soon as a message is posted.
     *
     * @param Message $message
     */
    public function handle(Message $message);

    /**
     * Will be called on kernel/console::terminate event.
     */
    public function close();
}
