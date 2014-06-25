<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\MetaSet;

/**
 * Meta set collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetCollection
{
    /**
     * @var MetaSet[]
     */
    private $metaSets = array();

    /**
     * @var array
     */
    private $titleMap = array();

    /**
     * Add meta set
     *
     * @param MetaSetInterface $metaSet
     * @return $this
     */
    public function add(MetaSetInterface $metaSet)
    {
        $this->metaSets[$metaSet->getId()] = $metaSet;
        $this->titleMap[$metaSet->getTitle()] = $metaSet->getId();

        return $this;
    }

    /**
     * @param string $id
     * @return MetaSetInterface
     */
    public function get($id)
    {
        if (isset($this->metaSets[$id])) {
            return $this->metaSets[$id];
        }

        return null;
    }

    /**
     * @param string $title
     * @return MetaSetInterface
     */
    public function getByTitle($title)
    {
        if (isset($this->titleMap[$title])) {
            return $this->get($this->titleMap[$title]);
        }

        return null;
    }

    /**
     * @return MetaSet[]
     */
    public function getAll()
    {
        return $this->metaSets;
    }
}
