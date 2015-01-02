<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\AttributeReader;

/**
 * Attribute bag
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AttributeBag implements \Countable
{
    /**
     * @var array
     */
    private $attributes = array();

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        if ($attributes) {
            $this->attributes = $attributes;
        }
    }

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

    /**
     * @param AttributeBag $attributes
     *
     * @return $this
     */
    public function merge(AttributeBag $attributes)
    {
        foreach ($attributes->all() as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->attributes);
    }
}
