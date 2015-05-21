<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\Distiller;

/**
 * Class map
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ClassMap
{
    private $containers = array();

    /**
     * {@inheritdoc}
     */
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
