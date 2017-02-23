<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\AccessControl;

use Phlexible\Bundle\TreeBundle\Entity\TreeNode;
use Phlexible\Component\AccessControl\Permission\Permission;
use Phlexible\Component\AccessControl\Permission\PermissionCollection;
use Phlexible\Component\AccessControl\Permission\PermissionProviderInterface;

/**
 * Tree permission provider.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreePermissionProvider implements PermissionProviderInterface
{
    /**
     * @var string
     */
    private $objectType;

    /**
     * @param string $objectType
     */
    public function __construct($objectType = TreeNode::class)
    {
        $this->objectType = $objectType;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return new PermissionCollection($this->objectType, array(
            new Permission('VIEW', 1),
            new Permission('EDIT', 2),
            new Permission('CREATE', 4),
            new Permission('DELETE', 8),
            new Permission('PUBLISH', 16),
            new Permission('ACCESS', 32),
        ));
    }
}
