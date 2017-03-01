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

use Phlexible\Bundle\MetaSetBundle\Entity\MetaDataValue;

/**
 * Meta data manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MetaDataManagerInterface
{
    /**
     * @param MetaSetInterface $metaSet
     *
     * @return MetaDataInterface[]
     */
    public function findByMetaSet(MetaSetInterface $metaSet);

    /**
     * @param string $value
     *
     * @return MetaDataValue[]
     */
    public function findRawByValue($value);

    /**
     * @param MetaSetFieldInterface $field
     *
     * @return MetaDataValue[]
     */
    public function findRawByField(MetaSetFieldInterface $field);

    /**
     * @param MetaSet $metaSet
     *
     * @return MetaDataInterface
     */
    public function createMetaData(MetaSet $metaSet);

    /**
     * @param mixed             $target
     * @param MetaDataInterface $metaData
     */
    public function updateMetaData($target, MetaDataInterface $metaData);
}
