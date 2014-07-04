<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Model;

use Phlexible\Bundle\MetaSetBundle\Entity\MetaSet;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaSetField;

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
     * @return MetaSet
     */
    public function find($id);

    /**
     * @param string $name
     *
     * @return MetaSet
     */
    public function findOneByName($name);

    /**
     * @return MetaSet[]
     */
    public function findAll();

    /**
     * @return MetaSet
     */
    public function createMetaSet();

    /**
     * @return MetaSetField
     */
    public function createMetaSetField();

    /**
     * @param MetaSet $metaSet
     */
    public function updateMetaSet(MetaSet $metaSet);
}