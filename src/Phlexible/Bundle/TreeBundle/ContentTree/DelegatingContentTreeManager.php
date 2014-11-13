<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Mediator\MediatorInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;

/**
 * Delegating content tree manager
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
     * @var XmlContentTree[]
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
     * @return DelegatingContentTree[]
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
     * @param int $treeId
     *
     * @return null|DelegatingContentTree
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
