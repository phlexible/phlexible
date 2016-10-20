<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaType\Model;

/**
 * Media type collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaTypeCollection
{
    /**
     * @var MediaType[]
     */
    private $mediaTypes = array();

    /**
     * @var array
     */
    private $mimetypeMap = array();

    /**
     * Add media type
     *
     * @param MediaType $mediaType
     *
     * @return $this
     */
    public function add(MediaType $mediaType)
    {
        $this->mediaTypes[$mediaType->getName()] = $mediaType;

        foreach ($mediaType->getMimetypes() as $mimetype) {
            $this->mimetypeMap[$mimetype] = $mediaType->getName();
        }

        return $this;
    }

    /**
     * Merge collection
     *
     * @param MediaTypeCollection $collection
     *
     * @return $this
     */
    public function merge(MediaTypeCollection $collection)
    {
        foreach ($collection->all() as $mediaType) {
            $this->add($mediaType);
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return MediaType
     */
    public function get($key)
    {
        if (isset($this->mediaTypes[$key])) {
            return $this->mediaTypes[$key];
        }

        return null;
    }

    /**
     * @param string $mimetype
     *
     * @return MediaType
     */
    public function getByMimetype($mimetype)
    {
        if (isset($this->mimetypeMap[$mimetype])) {
            return $this->get($this->mimetypeMap[$mimetype]);
        }

        return null;
    }

    /**
     * @return MediaType[]
     */
    public function all()
    {
        return $this->mediaTypes;
    }

    /**
     * @return MediaType
     */
    public function create()
    {
        return new MediaType();
    }

    /**
     * @return string
     */
    public function getHash()
    {
        $base = '';
        foreach ($this->mediaTypes as $mediaType) {
            $base .= md5(serialize($mediaType));
        }

        return md5($base);
    }
}
