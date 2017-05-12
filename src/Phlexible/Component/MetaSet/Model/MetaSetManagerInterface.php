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
 * Meta set manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MetaSetManagerInterface
{
    /**
     * @param string $id
     *
     * @return MetaSetInterface
     */
    public function find($id);

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return MetaSetInterface[]
     */
    public function findBy(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     *
     * @return MetaSetInterface
     */
    public function findOneBy(array $criteria = array(), array $orderBy = null);

    /**
     * @return MetaSetInterface[]
     */
    public function findAll();

    /**
     * @return MetaSetInterface
     */
    public function createMetaSet();

    /**
     * @return MetaSetFieldInterface
     */
    public function createMetaSetField();

    /**
     * @param MetaSetInterface $metaSet
     */
    public function updateMetaSet(MetaSetInterface $metaSet);
}
