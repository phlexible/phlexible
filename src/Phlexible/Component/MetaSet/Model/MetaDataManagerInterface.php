<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\Model;

/**
 * Meta data manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MetaDataManagerInterface
{
    /**
     * Load meta data
     *
     * @param MetaSet $metaSet
     * @param array   $identifiers
     *
     * @return MetaDataInterface
     */
    public function findByMetaSetAndIdentifiers(MetaSet $metaSet, array $identifiers);

    /**
     * @param MetaSet $metaSet
     *
     * @return MetaDataInterface
     */
    public function findByMetaSet(MetaSet $metaSet);

    /**
     * @return MetaDataInterface[]
     */
    public function findAll();

    /**
     * @param MetaSet $metaSet
     *
     * @return MetaDataInterface
     */
    public function createMetaData(MetaSet $metaSet);

    /**
     * @param MetaDataInterface $metaData
     */
    public function updateMetaData(MetaDataInterface $metaData);
}
