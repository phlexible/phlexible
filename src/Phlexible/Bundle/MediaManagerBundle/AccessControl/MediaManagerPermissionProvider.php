<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\AccessControl;

use Phlexible\Bundle\AccessControlBundle\Permission\Permission;
use Phlexible\Bundle\AccessControlBundle\Permission\PermissionCollection;
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
        return new PermissionCollection(
            array(
                new Permission('folder-internal', 'FOLDER_READ', 1, 'p-mediamanager-folder_view-icon'),
                new Permission('folder-internal', 'FOLDER_CREATE', 2, 'p-mediamanager-folder_add-icon'),
                new Permission('folder-internal', 'FOLDER_MODIFY', 4, 'p-mediamanager-folder_edit-icon'),
                new Permission('folder-internal', 'FOLDER_DELETE', 8, 'p-mediamanager-folder_delete-icon'),
                new Permission('folder-internal', 'FOLDER_RIGHTS', 16, 'p-mediamanager-folder_rights-icon'),
                new Permission('folder-internal', 'FILE_READ', 32, 'p-mediamanager-file_view-icon'),
                new Permission('folder-internal', 'FILE_CREATE', 64, 'p-mediamanager-file_add-icon'),
                new Permission('folder-internal', 'FILE_MODIFY', 128, 'p-mediamanager-file_edit-icon'),
                new Permission('folder-internal', 'FILE_DELETE', 256, 'p-mediamanager-file_delete-icon'),
                new Permission('folder-internal', 'FILE_DOWNLOAD', 512, 'p-mediamanager-file_download-icon'),
            )
        );
    }
}