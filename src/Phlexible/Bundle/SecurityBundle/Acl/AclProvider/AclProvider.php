<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Acl\AclProvider;

/**
 * ACL provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AclProvider implements AclProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function provideRoles()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function provideResources()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function provideAllow()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function provideDeny()
    {
        return array();
    }
}
