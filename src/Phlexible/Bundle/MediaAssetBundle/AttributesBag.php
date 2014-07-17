<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle;

/**
 * Asset meta bag
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AttributesBag
{
    /**
     * @var array
     */
    private $attributes = array();

    /**
     * @param string $key
     * @param string $defaultValue
     *
     * @return string
     */
    public function get($key, $defaultValue = null)
    {
        if ($this->has($key)) {
            return $this->attributes[$key];
        }

        return $defaultValue;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function remove($key)
    {
        if ($this->has($key)) {
            unset($this->attributes[$key]);
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->attributes;
    }
}