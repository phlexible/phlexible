<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Doctrine;

/**
 * Tree filter storage.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeFilterStorage
{
    /**
     * @var array
     */
    private $values = array();

    /**
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function get($key, $defaultValue = null)
    {
        if (!$this->has($key)) {
            return $defaultValue;
        }

        return $this->values[$key];
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->values[$key]);
    }

    public function clear()
    {
        $this->values = array();
    }
}
