<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\AclProvider;

use Phlexible\Bundle\SecurityBundle\Acl\AclProvider\AclProvider;

/**
 * Security acl provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SecurityAclProvider extends AclProvider
{
    /**
     * {@inheritdoc}
     */
    public function provideRoles()
    {
        return array(
            'anonymous',
            'user',
            'superadmin',
            'developer',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function provideResources()
    {
        return array(
            'login',
            'logout',
            'auth',
            'admin',
            'superadmin',
            'debug',
            'development',
            'testing',
            'roles',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function provideAllow()
    {
        return array(
            array('developer'),
            array('superadmin'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function provideDeny()
    {
        return array(
            array('superadmin', 'debug'),
            array('superadmin', 'development'),
            array('superadmin', 'testing'),
        );
    }
}