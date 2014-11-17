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
     * @var TreeNodeInterface
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
        $parentNode = $this->node->getParentNode();

        $children = [];
        $keep = true;
        foreach ($tree->getChildren($parentNode) as $childNode) {
            if ($childNode->getId() === $this->node->getId()) {
                $keep = false;
                continue;
            }
            if ($keep) {
                $child = new self($childNode, $this->referenceNode, $this->maxDepth, $this->depth + 1);
                if ($child->available()) {
                    $children[] = $child;
                }
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
        $parentNode = $this->node->getParentNode();

        $children = [];
        $keep = false;
        foreach ($tree->getChildren($parentNode) as $childNode) {
            if ($childNode->getId() === $this->node->getId()) {
                $keep = true;
                continue;
            }
            if ($keep) {
                $child = new self($childNode, $this->referenceNode, $this->maxDepth, $this->depth + 1);
                if ($child->available()) {
                    $children[] = $child;
                }
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
        $parentNode = $this->node->getParentNode();
        if (!$parentNode) {
            return null;
        }

        return new self(
            $this->node->getTree()->get($parentNode->getId()),
            $this->referenceNode,
            $this->maxDepth,
            $this->depth - 1
        );
    }

    /**
     * @return self[]
     */
    public function children()
    {
        if ($this->maxDepth && $this->depth >= $this->maxDepth) {
            return [];
        }

        $tree = $this->node->getTree();

        $children = [];
        foreach ($tree->getChildren($this->getNode()) as $childNode) {
            $child = new self($childNode, $this->referenceNode, $this->maxDepth, $this->depth + 1);
            if ($child->available()) {
                $children[] = $child;
            }
        }

        return $children;
    }

    /**
     * @return array
     */
    public function path()
    {
        $path = [];
        $current = $this;
        do {
            $path[] = $current;
        } while ($current = $current->parent());

        return array_reverse($path);
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
    public function available($language = null)
    {
        return $this->node->getTree()->isPublished($this->node, $language);
    }

    /**
     * @return bool
     */
    public function viewable()
    {
        return $this->node->getTree()->isViewable($this->node);
    }
}
