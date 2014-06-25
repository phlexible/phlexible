<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\AccessControl;

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
        return array(
            'internal' => array(
                'treenode' => array(
                    'VIEW' => array(
                        'iconCls' => 'p-element-view-icon',
                        'bit'     => 1,
                    ),
                    'EDIT' => array(
                        'iconCls' => 'p-element-edit-icon',
                        'bit'     => 2,
                    ),
                    'CREATE' => array(
                        'iconCls' => 'p-element-add-icon',
                        'bit'     => 4,
                    ),
                    'DELETE' => array(
                        'iconCls' => 'p-element-delete-icon',
                        'bit'     => 8,
                    ),
                    'PUBLISH' => array(
                        'iconCls' => 'p-element-publish-icon',
                        'bit'     => 16,
                    ),
                    'ACCESS' => array(
                        'iconCls' => 'p-element-tab_rights-icon',
                        'bit'     => 32,
                    ),
                ),
            ),
        );
    }
}