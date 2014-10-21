<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Tasks message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TasksMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaultChannel()
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaultRole()
    {
        return 'ROLE_TASKS';
    }
}