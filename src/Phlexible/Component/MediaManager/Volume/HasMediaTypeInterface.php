<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Volume;

/**
 * Has media type interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface HasMediaTypeInterface
{
    /**
     * @param string $mediaCategory
     *
     * @return $this
     */
    public function setMediaCategory($mediaCategory);

    /**
     * @return string
     */
    public function getMediaCategory();

    /**
     * @param string $mediaType
     *
     * @return $this
     */
    public function setMediaType($mediaType);

    /**
     * @return string
     */
    public function getMediaType();
}
