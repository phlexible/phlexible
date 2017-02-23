<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\Model;

/**
 * Meta set collection.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetCollection
{
    /**
     * @var MetaSetInterface[]
     */
    private $metaSets = array();

    /**
     * @var array
     */
    private $nameMap = array();

    /**
     * Add meta set.
     *
     * @param MetaSetInterface $metaSet
     *
     * @return $this
     */
    public function add(MetaSetInterface $metaSet)
    {
        $this->metaSets[$metaSet->getId()] = $metaSet;
        $this->nameMap[$metaSet->getName()] = $metaSet->getId();

        return $this;
    }

    /**
     * @param string $id
     *
     * @return MetaSetInterface
     */
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->metaSets[$id];
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return MetaSetInterface
     */
    public function getByName($name)
    {
        if (!isset($this->nameMap[$name])) {
            return null;
        }

        return $this->get($this->nameMap[$name]);
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        return isset($this->metaSets[$id]);
    }

    /**
     * @return MetaSetInterface[]
     */
    public function all()
    {
        return $this->metaSets;
    }
}
