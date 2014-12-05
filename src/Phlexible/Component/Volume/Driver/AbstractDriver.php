<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Volume\Driver;

use Phlexible\Component\Volume\VolumeInterface;

/**
 * Abstract driver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractDriver implements DriverInterface
{
    /**
     * @var VolumeInterface
     */
    private $volume;

    /**
     * {@inheritdoc}
     */
    public function setVolume(VolumeInterface $volume)
    {
        $this->volume = $volume;
    }

    /**
     * {@inheritdoc}
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeatures()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function hasFeature($name)
    {
        return in_array($name, $this->getFeatures());
    }
}
