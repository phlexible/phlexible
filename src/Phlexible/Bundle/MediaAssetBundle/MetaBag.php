<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle;

use Phlexible\Bundle\MetaSetBundle\MetaData\MetaDataInterface;

/**
 * Asset meta bag
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaBag
{
    /**
     * @var MetaDataInterface[]
     */
    private $metas = array();

    /**
     * @param string $id
     * @return MetaDataInterface
     */
    public function get($id)
    {
        if (isset($this->metas[$id])) {
            return $this->metas[$id];
        }

        return null;
    }

    /**
     * @param MetaDataInterface $metaData
     *
     * @return $this
     */
    public function add(MetaDataInterface $metaData)
    {
        if (count($metaData)) {
            $this->metas[$metaData->getTitle()] = $metaData;
        }

        return $this;
    }

    /**
     * @param MetaDataInterface $metaData
     *
     * @return $this
     */
    public function remove(MetaDataInterface $metaData)
    {
        if ($this->has($metaData)) {
            unset($this->metas[$metaData->getTitle()]);
        }

        return $this;
    }

    /**
     * @param MetaDataInterface $metaData
     *
     * @return boolean
     */
    public function has(MetaDataInterface $metaData)
    {
        return isset($this->metas[$metaData->getTitle()]);
    }

    /**
     * @return MetaDataInterface[]
     */
    public function getAll()
    {
        return $this->metas;
    }
}