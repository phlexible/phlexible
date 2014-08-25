<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Content tree context
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentTreeContext
{
    /**
     * @var \Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface
     */
    private $node;

    /**
     * @var TreeNodeInterface
     */
    private $referenceNode;

    /**
     * @var int
     */
    private $maxDepth;

    /**
     * @var int
     */
    private $depth;

    /**
     * @param ContentTreeNode $node
     * @param ContentTreeNode $referenceNode
     * @param int             $maxDepth
     * @param int             $depth
     */
    public function __construct(
        ContentTreeNode $node,
        ContentTreeNode $referenceNode = null,
        $maxDepth = null,
        $depth = 0)
    {
        $this->node = $node;
        $this->referenceNode = $referenceNode;
        $this->maxDepth = $maxDepth;
        $this->depth = $depth;
    }

    /**
     * @return TreeNodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return self[]
     */
    public function siblings()
    {
        return array_merge($this->before(), $this->after());
    }

    /**
     * @return self[]
     */
    public function before()
    {
        $tree = $this->node->getTree();
        $parentNode = $tree->get($this->node->getParentId());

        $children = array();
        $keep = true;
        foreach ($tree->getChildren($parentNode) as $childNode) {
            if ($childNode->getId() === $this->node->getId()) {
                $keep = false;
                continue;
            }
            if ($keep) {
                $children[] = new self($childNode, $this->referenceNode, $this->maxDepth, $this->depth + 1);
            }
        }

        return $children;
    }

    /**
     * @return self[]
     */
    public function after()
    {
        $tree = $this->node->getTree();
        $parentNode = $tree->get($this->node->getParentId());

        $children = array();
        $keep = false;
        foreach ($tree->getChildren($parentNode) as $childNode) {
            if ($childNode->getId() === $this->node->getId()) {
                $keep = true;
                continue;
            }
            if ($keep) {
                $children[] = new self($childNode, $this->referenceNode, $this->maxDepth, $this->depth + 1);
            }
        }

        return $children;
    }

    /**
     * @return self|null
     */
    public function previous()
    {
        $before = $this->before();
        if (!count($before)) {
            return null;
        }

        return end($before);
    }

    /**
     * @return self|null
     */
    public function next()
    {
        $after = $this->after();
        if (!count($after)) {
            return null;
        }

        return current($after);
    }

    /**
     * @return self|null
     */
    public function parent()
    {
        return new self($this->node->getTree()->getParent(
            $this->node
        ), $this->referenceNode, $this->maxDepth, $this->depth - 1);
    }

    /**
     * @return self[]
     */
    public function children()
    {
        if ($this->maxDepth !== -1 && $this->depth >= $this->maxDepth) {
            return array();
        }

        $tree = $this->node->getTree();

        $children = array();
        foreach ($tree->getChildren($this->getNode()) as $childNode) {
            $children[] = new self($childNode, $this->referenceNode, $this->maxDepth, $this->depth + 1);
        }

        return $children;
    }

    /**
     * @return bool
     */
    public function active()
    {
        if (!$this->referenceNode) {
            return false;
        }

        if ($this->node->getId() === $this->referenceNode->getId()) {
            return true;
        }

        return $this->node->getTree()->isParentOf($this->node, $this->referenceNode);
    }

    /**
     * @param string $language
     *
     * @return bool
     */
    public function online($language)
    {
        // TODO: fix
        return true;//$this->node->hasVersion($language);
    }
}
