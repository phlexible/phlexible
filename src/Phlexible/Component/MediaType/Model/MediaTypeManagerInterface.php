<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
     * Find document type
     *
     * @param string $key
     *
     * @return MediaType
     */
    public function find($key);

    /**
     * Return document type by filename
     *
     * @param string $filename
     *
     * @return MediaType
     */
    public function findByFilename($filename);

    /**
     * Return document type by mimetype
     *
     * @param string $mimetype
     *
     * @return MediaType
     */
    public function findByMimetype($mimetype);

    /**
     * Return all document types
     *
     * @return MediaType[]
     */
    public function findAll();
}
