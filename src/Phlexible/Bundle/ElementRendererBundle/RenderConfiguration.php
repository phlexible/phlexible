<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle;

/**
 * Element renderer configuration
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RenderConfiguration
{
    /**
     * @var array
     */
    private $values = array();

    /**
     * @var array
     */
    private $features = array();

    /**
     * @param string $key
     * @param mixed  $value
     * @param string $feature
     *
     * @return $this
     */
    public function set($key, $value, $feature = null)
    {
        $this->values[$key] = $value;

        if ($feature) {
            $this->addFeature($feature);
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function addFeature($name)
    {
        $this->features[] = $name;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasFeature($name)
    {
        return in_array($name, $this->features);
    }
}