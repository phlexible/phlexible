<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\Model;

/**
 * Meta set manager interface
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
     * @param string $name
     *
     * @return MetaSetInterface
     */
    public function findOneByName($name);

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
