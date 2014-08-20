<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle;

use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Exception\NodeNotFoundException;
use Phlexible\Bundle\TreeBundle\Tree\TreeFactoryInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeInterface;
use Phlexible\Bundle\TreeBundle\Tree\WritableTreeInterface;

/**
 * Tree manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeManager
{
    /**
     * @var TreeInterface[]
     */
    private $trees = array();

    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @var TreeFactoryInterface
     */
    private $treeFactory;

    /**
     * @param SiterootManagerInterface $siterootManager
     * @param TreeFactoryInterface     $treeFactory
     */
    public function __construct(SiterootManagerInterface $siterootManager, TreeFactoryInterface $treeFactory)
    {
        $this->siterootManager = $siterootManager;
        $this->treeFactory = $treeFactory;
    }

    /**
     * Return tree by siteroot ID
     *
     * @param string $siteRootId
     *
     * @return TreeInterface|WritableTreeInterface
     */
    public function getBySiteRootId($siteRootId)
    {
        if (!isset($this->trees[$siteRootId])) {
            $tree = $this->treeFactory->factory($siteRootId);
            $this->trees[$siteRootId] = $tree;
        }

        return $this->trees[$siteRootId];
    }

    /**
     * Get tree by node ID
     *
     * @param int $nodeId
     *
     * @return TreeInterface|WritableTreeInterface
     * @throws NodeNotFoundException
     */
    public function getByNodeId($nodeId)
    {
        foreach ($this->siterootManager->findAll() as $siteroot) {
            $tree = $this->getBySiteRootId($siteroot->getId());

            if ($tree->has($nodeId)) {
                return $tree;
            }
        }

        throw new NodeNotFoundException('Tree for node  "' . $nodeId . '" not found.');
    }
}
