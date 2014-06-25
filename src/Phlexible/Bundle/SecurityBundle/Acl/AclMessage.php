<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Acl;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * ACL message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AclMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public function getDefaults()
    {
        return array(
            'component' => 'security',
            'resources' => 'acl'
        );
    }
}