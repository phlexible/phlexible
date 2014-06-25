<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Acl;

use Phlexible\Bundle\SecurityBundle\Acl\AclProvider\AclProviderInterface;

/**
 * ACL factory
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AclFactory
{
    /**
     * @param AclProviderInterface $provider
     *
     * @return Acl
     */
    public static function factory(AclProviderInterface $provider)
    {
        $acl = new Acl();

        foreach ($provider->provideRoles() as $roleDefinition) {
            $acl->addRoleIfNonExistant($roleDefinition);
        }

        foreach ($provider->provideResources() as $resourceDefinition) {
            $acl->addResourceIfNonExistant($resourceDefinition);
        }

        foreach ($provider->provideAllow() as $denyDefinition) {
            $role = $denyDefinition[0];
            $resource = !empty($denyDefinition[1]) ? $denyDefinition[1] : null;
            $privilege = !empty($denyDefinition[2]) ? $denyDefinition[2] : null;

            if (!$acl->hasRole($role)) {
                continue;
            }
            if ($resource && !$acl->has($resource)) {
                continue;
            }

            $acl->allow($role, $resource, $privilege);
        }

        foreach ($provider->provideDeny() as $denyDefinition) {
            $role = $denyDefinition[0];
            $resource = !empty($denyDefinition[1]) ? $denyDefinition[1] : null;
            $privilege = !empty($denyDefinition[2]) ? $denyDefinition[2] : null;

            if (!$acl->hasRole($role)) {
                continue;
            }
            if ($resource && !$acl->has($resource)) {
                continue;
            }

            $acl->deny($role, $resource, $privilege);
        }

        return $acl;
    }
}
