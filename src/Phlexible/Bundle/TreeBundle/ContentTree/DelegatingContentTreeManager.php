<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Mediator\MediatorInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;

/**
 * Delegating content tree manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingContentTreeManager implements ContentTreeManagerInterface
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @var TreeManager
     */
    private $treeManager;

    /**
     * @var MediatorInterface
     */
    private $mediator;

    /**
     * @var DelegatingContentTree[]
     */
    private $trees;

    /**
     * @param SiterootManagerInterface $siterootManager
     * @param TreeManager              $treeManager
     * @param MediatorInterface        $mediator
     */
    public function __construct(SiterootManagerInterface $siterootManager, TreeManager $treeManager, MediatorInterface $mediator)
    {
        $this->siterootManager = $siterootManager;
        $this->treeManager = $treeManager;
        $this->mediator = $mediator;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        if (null === $this->trees) {
            $this->trees = [];
            foreach ($this->siterootManager->findAll() as $siteroot) {
                $tree = $this->treeManager->getBySiteRootId($siteroot->getId());
                $this->trees[] = new DelegatingContentTree($tree, $siteroot, $this->mediator);
            }
        }

        return $this->trees;
    }

    /**
     * {@inheritdoc}
     */
    public function find($siterootId)
    {
        $trees = $this->findAll();
        if (!$trees) {
            return null;
        }

        foreach ($trees as $tree) {
            if ($tree->getSiterootId() === $siterootId) {
                return $tree;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findByTreeId($treeId)
    {
        $trees = $this->findAll();
        if (!$trees) {
            return null;
        }

        foreach ($trees as $tree) {
            if ($tree->has($treeId)) {
                return $tree;
            }
        }

        return null;
    }
}
