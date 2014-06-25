<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\AclProvider;

use Phlexible\Bundle\SecurityBundle\Acl\AclProvider\AclProvider;

/**
 * Task acl provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TaskAclProvider extends AclProvider
{
    /**
     * {@inheritdoc}
     */
    public function provideResources()
    {
        return array(
            'tasks',
        );
    }
}