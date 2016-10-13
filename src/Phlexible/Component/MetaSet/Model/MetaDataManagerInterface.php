<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\Model;

use Phlexible\Bundle\MetaSetBundle\Entity\MetaDataValue;

/**
 * Meta data manager interface
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
     * @return MetaDataInterface[]
     */
    public function findByValue($value);

    /**
     * @param MetaSetFieldInterface $field
     *
     * @return MetaDataValue[]
     */
    public function findByField(MetaSetFieldInterface $field);

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
