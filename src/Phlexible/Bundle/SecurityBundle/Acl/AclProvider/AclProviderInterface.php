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
interface AclProviderInterface
{
    const DEFINITION_RESOURCES = 'resources';
    const DEFINITION_ROLES = 'roles';
    const DEFINITION_ALLOW = 'allow';
    const DEFINITION_DENY = 'deny';

    /**
     * Provide acl allow rules
     *
     * @return array
     */
    public function provideAllow();

    /**
     * Provide acl roles
     *
     * @return array
     */
    public function provideRoles();

    /**
     * Provide acl resources
     *
     * @return array
     */
    public function provideResources();

    /**
     * Provide acl deny rules
     *
     * @return array
     */
    public function provideDeny();
}