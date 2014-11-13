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
     * @var string
     */
    private $contentClass;

    /**
     * @param string $contentClass
     */
    public function __construct($contentClass = 'Phlexible\Bundle\TreeBundle\Entity\TreeNode')
    {
        $this->contentClass = $contentClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return new PermissionCollection(array(
            new Permission($this->contentClass, 'VIEW', 1, 'p-element-view-icon'),
            new Permission($this->contentClass, 'EDIT', 2, 'p-element-edit-icon'),
            new Permission($this->contentClass, 'CREATE', 4, 'p-element-add-icon'),
            new Permission($this->contentClass, 'DELETE', 8, 'p-element-delete-icon'),
            new Permission($this->contentClass, 'PUBLISH', 16, 'p-element-publish-icon'),
            new Permission($this->contentClass, 'ACCESS', 32, 'p-element-tab_rights-icon'),
        ));
    }
}
