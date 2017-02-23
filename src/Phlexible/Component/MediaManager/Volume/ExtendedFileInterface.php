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

use Phlexible\Component\Volume\Model\FileInterface;

/**
 * File interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ExtendedFileInterface extends FileInterface
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

    /**
     * @param array $metasets
     *
     * @return $this
     */
    public function setMetasets(array $metasets);

    /**
     * @return array
     */
    public function getMetasets();

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function addMetaSet($metaSetId);

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function removeMetaSet($metaSetId);
}
