<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume\Driver;

use Phlexible\Component\Volume\VolumeInterface;

/**
 * Abstract driver.
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
