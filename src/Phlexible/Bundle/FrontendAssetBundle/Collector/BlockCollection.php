<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendAssetBundle\Collector;

/**
 * Block Collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BlockCollection
{
    /**
     * @var array
     */
    private $blocks = array();

    /**
     * @return array
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasBlock($name)
    {
        return isset($this->blocks[$name]);
    }

    /**
     * @param string $name
     *
     * @return Block
     */
    public function getBlock($name)
    {
        if (!$this->hasBlock($name)) {
            $this->blocks[$name] = new Block($name);
        }

        return $this->blocks[$name];
    }
}
