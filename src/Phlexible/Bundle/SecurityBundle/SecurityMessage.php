<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Security message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SecurityMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public function getDefaults()
    {
        return array(
            'component' => 'security',
            'resources' => 'auth'
        );
    }
}