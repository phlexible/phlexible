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
 * Message manager handler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageManagerHandler implements HandlerInterface
{
    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(MessageManagerInterface $messageManager)
    {
        $this->messageManager = $messageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Message $message)
    {
        $this->messageManager->updateMessage($message);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
    }
}
