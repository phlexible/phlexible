<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaType\File;

use Phlexible\Component\MediaType\Model\MediaTypeCollection;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Phlexible\Component\Mime\MimeDetector;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Media type manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaTypeManager implements MediaTypeManagerInterface
{
    /**
     * @var PuliLoader
     */
    private $loader;

    /**
     * @var MediaTypeCollection
     */
    private $mediaTypes;

    /**
     * @param PuliLoader $loader
     */
    public function __construct(PuliLoader $loader)
    {
        $this->loader = $loader;
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
            $this->mediaTypes = $this->loader->loadMediaTypes();
        }

        return $this->mediaTypes;
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
        $file = new File($filename);
        $mimetype = $file->getMimeType();

        return $this->findByMimetype($mimetype);
    }
}
