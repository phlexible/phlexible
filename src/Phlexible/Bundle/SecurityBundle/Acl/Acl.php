<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Acl;

use Zend_Acl as BaseAcl;

/**
 * ACL
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Acl extends BaseAcl
{
    const ROLE_ANONYMOUS   = 'ANONYMOUS';
    const ROLE_USER        = 'USER';
    const ROLE_SUPERADMIN  = 'SUPERADMIN';
    const ROLE_DEVELOPER   = 'DEVELOPER';

    /* Developer specific */
    const RESOURCE_DEBUG       = 'debug';
    const RESOURCE_DEVELOPMENT = 'development';
    const RESOURCE_TESTING     = 'testing';

    /* Superadmin specific */
    const RESOURCE_SUPERADMIN = 'superadmin';
    const RESOURCE_ADMIN = 'admin';

    /**
     * @param string $role
     *
     * @return $this
     */
    public function addRoleIfNonExistant($role)
    {
        if (!$this->hasRole($role)) {
            parent::addRole($role);
        }

        return $this;
    }

    /**
     * @param string $resource
     *
     * @return $this
     */
    public function addResourceIfNonExistant($resource)
    {
        if (!$this->has($resource)) {
            parent::addResource($resource);
        }

        return $this;
    }
}
