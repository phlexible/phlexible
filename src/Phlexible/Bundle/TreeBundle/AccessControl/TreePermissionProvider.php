<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\AccessControl;

use Phlexible\Bundle\AccessControlBundle\Permission\Permission;
use Phlexible\Bundle\AccessControlBundle\Permission\PermissionCollection;
use Phlexible\Bundle\AccessControlBundle\Permission\PermissionProviderInterface;

/**
 * Tree permission provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreePermissionProvider implements PermissionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return new PermissionCollection(
            array(
                new Permission('treenode-internal', 'VIEW', 1, 'p-element-view-icon'),
                new Permission('treenode-internal', 'EDIT', 2, 'p-element-edit-icon'),
                new Permission('treenode-internal', 'CREATE', 4, 'p-element-add-icon'),
                new Permission('treenode-internal', 'DELETE', 8, 'p-element-delete-icon'),
                new Permission('treenode-internal', 'PUBLISH', 16, 'p-element-publish-icon'),
                new Permission('treenode-internal', 'ACCESS', 32, 'p-element-tab_rights-icon'),
            )
        );
    }
}