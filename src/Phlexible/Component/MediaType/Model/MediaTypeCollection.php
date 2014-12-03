<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
            $this->mimetypeMap[$mimetype] = $mediaType->getKey();
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
        foreach ($collection->all() as $documenttype) {
            $this->add($documenttype);
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
        foreach ($this->mediaTypes as $documenttype) {
            $base .= md5(serialize($documenttype));
        }

        return md5($base);
    }
}
