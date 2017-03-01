<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Phlexible\Bundle\ElementBundle\Element\ElementHasher;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Node hasher.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeHasher
{
    /**
     * @var ElementHasher
     */
    private $elementHasher;

    /**
     * @var string
     */
    private $algo;

    /**
     * @var array
     */
    private $hashes = [];

    /**
     * @param ElementHasher $elementHasher
     * @param string        $algo
     */
    public function __construct(ElementHasher $elementHasher, $algo = 'md5')
    {
        $this->elementHasher = $elementHasher;
        $this->algo = $algo;
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param int               $version
     * @param string            $language
     *
     * @return string
     */
    public function hashNode(TreeNodeInterface $treeNode, $version, $language)
    {
        $identifier = "{$treeNode->getId()}__{$version}__{$language}";

        if (isset($this->hashes[$identifier])) {
            return $this->hashes[$identifier];
        }

        $values = $this->createHashValuesByTreeNode($treeNode, $version, $language);
        $hash = $this->hashValues($values);

        $this->hashes[$identifier] = $hash;

        return $hash;
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param int               $version
     * @param string            $language
     *
     * @return array
     */
    private function createHashValuesByTreeNode(TreeNodeInterface $treeNode, $version, $language)
    {
        $eid = $treeNode->getTypeId();

        $attributes = $treeNode->getAttributes();
        $attributes['navigation'] = $treeNode->getInNavigation();

        $values = $this->elementHasher->createHashValuesByEid($eid, $version, $language);
        $values['attributes'] = $attributes;

        return $values;
    }

    /**
     * @param array $values
     *
     * @return string
     */
    private function hashValues(array $values)
    {
        return hash($this->algo, serialize($values));
    }
}
