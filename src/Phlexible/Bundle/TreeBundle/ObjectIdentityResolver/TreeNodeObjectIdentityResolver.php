<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\ObjectIdentityResolver;

use Phlexible\Bundle\TreeBundle\Entity\TreeNode;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Phlexible\Component\AccessControl\Domain\HierarchicalObjectIdentity;
use Phlexible\Component\AccessControl\ObjectIdentityResolver\ObjectIdentityResolverInterface;

/**
 * Folder object identity resolver.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeNodeObjectIdentityResolver implements ObjectIdentityResolverInterface
{
    /**
     * @var TreeManager
     */
    private $treeManager;

    /**
     * @param TreeManager $treeManager
     */
    public function __construct(TreeManager $treeManager)
    {
        $this->treeManager = $treeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($objectType, $objectId)
    {
        if ($objectType !== TreeNode::class) {
            return null;
        }

        $tree = $this->treeManager->getByNodeId($objectId);
        $node = $tree->get($objectId);

        return HierarchicalObjectIdentity::fromDomainObject($node);
    }
}
