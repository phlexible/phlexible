<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Component\MediaManager\Volume\ExtendedFileVersionInterface;
use Phlexible\Component\Volume\Model\FileVersion as BaseFileVersion;

/**
 * File version.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="media_file_version")
 */
class FileVersion extends BaseFileVersion implements ExtendedFileVersionInterface
{
    /**
     * @var string
     * @ORM\Column(name="media_category", type="string")
     */
    private $mediaCategory;

    /**
     * @var string
     * @ORM\Column(name="media_type", type="string")
     */
    private $mediaType;

    /**
     * @var array
     * @ORM\Column(name="metasets", type="simple_array", nullable=true)
     */
    private $metasets = [];

    /**
     * @param string $mediaCategory
     *
     * @return $this
     */
    public function setMediaCategory($mediaCategory)
    {
        $this->mediaCategory = $mediaCategory;

        return $this;
    }

    /**
     * @return string
     */
    public function getMediaCategory()
    {
        return $this->mediaCategory;
    }

    /**
     * @param string $mediaType
     *
     * @return $this
     */
    public function setMediaType($mediaType)
    {
        $this->mediaType = $mediaType;

        return $this;
    }

    /**
     * @return string
     */
    public function getMediaType()
    {
        return $this->mediaType;
    }

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function addMetaSet($metaSetId)
    {
        if ($metaSetId && !in_array($metaSetId, $this->metasets)) {
            $this->metasets[] = $metaSetId;
        }

        return $this;
    }

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function removeMetaSet($metaSetId)
    {
        if (in_array($metaSetId, $this->metasets)) {
            unset($this->metasets[array_search($metaSetId, $this->metasets)]);
        }

        return $this;
    }

    /**
     * @param array $metasets
     *
     * @return $this
     */
    public function setMetaSets(array $metasets)
    {
        $this->metasets = $metasets;

        return $this;
    }

    /**
     * @return array
     */
    public function getMetaSets()
    {
        return $this->metasets;
    }
}
