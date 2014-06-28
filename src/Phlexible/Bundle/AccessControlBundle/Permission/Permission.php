<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Permission;

use Phlexible\Bundle\AccessControlBundle\Exception\InvalidArgumentException;

/**
 * Permission
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Permission
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $bit;

    /**
     * @var array
     */
    private $iconClass;

    /**
     * @param string $type
     * @param string $name
     * @param int    $bit
     * @param string $iconClass
     *
     * @throws InvalidArgumentException
     */
    public function __construct($type, $name, $bit, $iconClass)
    {
        // check for single bit (power of 2), see: http://aggregate.org/MAGIC/#Is%20Power%20of%202
        if ($bit & ($bit - 1)) {
            $bin = decbin($bit);
            throw new InvalidArgumentException("Only a single bit can be set, but got $bin in $type-$name");
        }

        $this->type = $type;
        $this->name = $name;
        $this->bit = $bit;
        $this->iconClass = $iconClass;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return bit
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
        return $this->bit & (int) $value > 0;
    }

    /**
     * @return string
     */
    public function getIconClass()
    {
        return $this->iconClass;
    }
}
