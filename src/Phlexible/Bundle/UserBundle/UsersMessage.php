<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Users message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UsersMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public function getDefaults()
    {
        return array(
            'component' => 'users',
            'resource'  => 'users'
        );
    }
}