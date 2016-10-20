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
 * Media type manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MediaTypeManagerInterface
{
    /**
     * @return MediaTypeCollection
     */
    public function getCollection();

    /**
     * Return new media type
     *
     * @return MediaType
     */
    public function create();

    /**
     * Find media type
     *
     * @param string $key
     *
     * @return MediaType
     */
    public function find($key);

    /**
     * Return media type by filename
     *
     * @param string $filename
     *
     * @return MediaType
     */
    public function findByFilename($filename);

    /**
     * Return media type by mimetype
     *
     * @param string $mimetype
     *
     * @return MediaType
     */
    public function findByMimetype($mimetype);

    /**
     * Return all media types
     *
     * @return MediaType[]
     */
    public function findAll();
}
