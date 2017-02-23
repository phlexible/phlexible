<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\Permission;

use Phlexible\Component\AccessControl\Exception\InvalidArgumentException;

/**
 * Permission.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Permission
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $bit;

    /**
     * @param string $name
     * @param int    $bit
     *
     * @throws InvalidArgumentException
     */
    public function __construct($name, $bit)
    {
        // check for single bit (power of 2), see: http://aggregate.org/MAGIC/#Is%20Power%20of%202
        if ($bit & ($bit - 1)) {
            $bin = decbin($bit);
            throw new InvalidArgumentException("Only a single bit can be set, but got $bin in permission $name");
        }

        $this->name = $name;
        $this->bit = $bit;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return bit.
     *
     * @return int
     */
    public function getBit()
    {
        return $this->bit;
    }

    /**
     * @param int $value
     *
     * @return bool
     */
    public function test($value)
    {
        return ($this->bit & (int) $value) > 0;
    }
}
