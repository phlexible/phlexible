<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Distiller;

/**
 * Class map.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ClassMap
{
    private $containers = array();

    public function add($name, $value)
    {
        $this->containers[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->containers[$name]);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->containers;
    }
}
