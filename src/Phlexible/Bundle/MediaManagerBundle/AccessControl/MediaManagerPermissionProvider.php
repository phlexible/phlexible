<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\AccessControl;

use Phlexible\Component\AccessControl\Permission\Permission;
use Phlexible\Component\AccessControl\Permission\PermissionCollection;
use Phlexible\Component\AccessControl\Permission\PermissionProviderInterface;

/**
 * Media manager permission provider.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaManagerPermissionProvider implements PermissionProviderInterface
{
    /**
     * @var string
     */
    private $objectType;

    /**
     * @param string $objectType
     */
    public function __construct($objectType = 'Phlexible\Bundle\MediaManagerBundle\Entity\Folder')
    {
        $this->objectType = $objectType;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return new PermissionCollection($this->objectType, array(
            new Permission('FOLDER_READ', 1),
            new Permission('FOLDER_CREATE', 2),
            new Permission('FOLDER_MODIFY', 4),
            new Permission('FOLDER_DELETE', 8),
            new Permission('FOLDER_RIGHTS', 16),
            new Permission('FILE_READ', 32),
            new Permission('FILE_CREATE', 64),
            new Permission('FILE_MODIFY', 128),
            new Permission('FILE_DELETE', 256),
            new Permission('FILE_DOWNLOAD', 512),
        ));
    }
}
