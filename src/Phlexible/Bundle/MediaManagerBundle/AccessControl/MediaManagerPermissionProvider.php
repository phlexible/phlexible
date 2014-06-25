<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\AccessControl;

use Phlexible\Bundle\AccessControlBundle\Permission\PermissionProviderInterface;

/**
 * Media manager permission provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaManagerPermissionProvider implements PermissionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return array(
            'internal' => array(
                'folder' => array(
                    'FOLDER_READ' => array(
                        'bit' => 1,
                        'iconCls' => 'p-mediamanager-folder_view-icon'
                    ),
                    'FOLDER_CREATE' => array(
                        'bit' => 2,
                        'iconCls' => 'p-mediamanager-folder_add-icon'
                    ),
                    'FOLDER_MODIFY' => array(
                        'bit' => 4,
                        'iconCls' => 'p-mediamanager-folder_edit-icon'
                    ),
                    'FOLDER_DELETE' => array(
                        'bit' => 8,
                        'iconCls' => 'p-mediamanager-folder_delete-icon'
                    ),
                    'FOLDER_RIGHTS' => array(
                        'bit' => 16,
                        'iconCls' => 'p-mediamanager-folder_rights-icon'
                    ),
                    'FILE_READ' => array(
                        'bit' => 32,
                        'iconCls' => 'p-mediamanager-file_view-icon'
                    ),
                    'FILE_CREATE' => array(
                        'bit' => 64,
                        'iconCls' => 'p-mediamanager-file_add-icon'
                    ),
                    'FILE_MODIFY' => array(
                        'bit' => 128,
                        'iconCls' => 'p-mediamanager-file_edit-icon'
                    ),
                    'FILE_DELETE' => array(
                        'bit' => 256,
                        'iconCls' => 'p-mediamanager-file_delete-icon'
                    ),
                    'FILE_DOWNLOAD' => array(
                        'bit' => 512,
                        'iconCls' => 'p-mediamanager-file_download-icon'
                    ),
                ),
            ),
        );
    }
}