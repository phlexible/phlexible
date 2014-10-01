<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Config;

use Phlexible\Bundle\GuiBundle\Exception\InvalidArgumentException;

/**
 * Config
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Config
{
    /**
     * @var array
     */
    private $values = array();

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @throws InvalidArgumentException
     * @return $this
     */
    public function set($key, $value)
    {
        if (!is_scalar($value) && !is_array($value)) {
            throw new InvalidArgumentException('Value has to be a scalar or an array, but is ' . gettype(
                    $value
                ) . '.');
        }

        $this->values[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @throws InvalidArgumentException
     * @return mixed
     */
    public function get($key)
    {
        if (!isset($this->values[$key])) {
            throw new InvalidArgumentException("Key $key not set.");
        }

        return $this->values[$key];
    }

    /**
     * @return array
     */
    public function getAll()
    {
        ksort($this->values);

        return $this->values;
    }
}
