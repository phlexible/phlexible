<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\File;

use Phlexible\Component\MetaSet\Model\MetaSet;
use Phlexible\Component\MetaSet\Model\MetaSetCollection;
use Phlexible\Component\MetaSet\Model\MetaSetField;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * File meta set manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetManager implements MetaSetManagerInterface
{
    /**
     * @var MetaSetLoader
     */
    private $loader;

    /**
     * @var MetaSetDumper
     */
    private $dumper;

    /**
     * @var MetaSetCollection
     */
    private $metaSets;

    /**
     * @param MetaSetLoader $loader
     * @param MetaSetDumper $dumper
     */
    public function __construct(MetaSetLoader $loader, MetaSetDumper $dumper)
    {
        $this->loader = $loader;
        $this->dumper = $dumper;
    }

    /**
     * @return MetaSetCollection
     */
    public function getCollection()
    {
        if ($this->metaSets === null) {
            $this->metaSets = $this->loader->loadMetaSets();
        }

        return $this->metaSets;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getCollection()->get($id);
    }

    /**
     * @param string $name
     *
     * @return MetaSet
     */
    public function findOneByName($name)
    {
        return $this->getCollection()->getByName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getCollection()->all();
    }

    /**
     * {@inheritdoc}
     */
    public function createMetaSet()
    {
        return new MetaSet();
    }

    /**
     * {@inheritdoc}
     */
    public function createMetaSetField()
    {
        return new MetaSetField();
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetaSet(MetaSetInterface $metaSet)
    {
        $this->dumper->dumpMetaSet($metaSet);
    }
}
