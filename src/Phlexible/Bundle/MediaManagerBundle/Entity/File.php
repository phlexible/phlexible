<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\Volume\Model\File as BaseFile;

/**
 * File
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="media_file")
 */
class File extends BaseFile implements ExtendedFileInterface
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
