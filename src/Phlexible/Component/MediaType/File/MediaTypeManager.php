<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaType\File;

use Brainbits\Mime\MimeDetector;
use Phlexible\Component\MediaType\Model\MediaTypeCollection;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;

/**
 * Media type manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaTypeManager implements MediaTypeManagerInterface
{
    /**
     * @var MediaTypeLoader
     */
    private $mediaTypeLoader;

    /**
     * @var MimeDetector
     */
    private $mimeDetector;

    /**
     * @var MediaTypeCollection
     */
    private $mediaTypes;

    /**
     * @param MediaTypeLoader $mediaTypeLoader
     * @param MimeDetector    $mimeDetector
     */
    public function __construct(MediaTypeLoader $mediaTypeLoader, MimeDetector $mimeDetector)
    {
        $this->mediaTypeLoader = $mediaTypeLoader;
        $this->mimeDetector = $mimeDetector;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->getCollection()->create();
    }

    /**
     * @return MediaTypeCollection
     */
    public function getCollection()
    {
        if ($this->mediaTypes === null) {
            $this->mediaTypes = $this->mediaTypeLoader->loadMediaTypes();
        }

        return $this->mediaTypes;
    }

    /**
     * @return MimeDetector
     */
    public function getMimeDetector()
    {
        return $this->mimeDetector;
    }

    /**
     * {@inheritdoc}
     */
    public function find($key)
    {
        return $this->getCollection()->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getCollection()->all();
    }

    /**
     * {@inheritdoc}
     */
    public function findByMimetype($mimetype)
    {
        return $this->getCollection()->getByMimetype($mimetype);
    }

    /**
     * {@inheritdoc}
     */
    public function findByFilename($filename)
    {
        $mimetype = $this->mimeDetector->detect($filename, MimeDetector::RETURN_STRING);

        return $this->findByMimetype($mimetype);
    }
}
