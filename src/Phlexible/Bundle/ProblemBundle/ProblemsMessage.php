<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Problems message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ProblemsMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaultChannel()
    {
        return 'problems';
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaultRole()
    {
        return 'ROLE_PROBLEMS';
    }
}
