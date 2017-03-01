<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Config;

use Phlexible\Bundle\GuiBundle\Exception\InvalidArgumentException;

/**
 * Config.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Config
{
    /**
     * @var array
     */
    private $values = [];

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function set($key, $value)
    {
        if (!is_scalar($value) && !is_array($value)) {
            $msg = 'Value has to be a scalar or an array, but is '.gettype($value).'.';
            throw new InvalidArgumentException($msg);
        }

        $this->values[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new InvalidArgumentException("Config key $key not set.");
        }

        return $this->values[$key];
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

    /**
     * @param string $key
     *
     * @return bool
     */
    public function remove($key)
    {
        if ($this->has($key)) {
            unset($this->values[$key]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        ksort($this->values);

        return $this->values;
    }
}
