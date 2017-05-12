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

use Phlexible\Component\MetaSet\Domain\MetaSetCollection;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;

/**
 * Meta set repository interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MetaSetRepositoryInterface
{
    /**
     * @return MetaSetCollection
     */
    public function loadAll();

    /**
     * @param string $id
     *
     * @return MetaSetInterface
     */
    public function load($id);

    /**
     * @param string $name
     *
     * @return MetaSetInterface
     */
    public function loadByName($name);

    /**
     * @param MetaSetInterface $metaSet
     * @param string           $type
     */
    public function writeMetaSet(MetaSetInterface $metaSet, $type = null);
}
