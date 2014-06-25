<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\Model;

use Phlexible\Bundle\DocumenttypeBundle\Exception\NotFoundException;

/**
 * Documenttype manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DocumenttypeManagerInterface
{
    /**
     * @return DocumenttypeCollection
     */
    public function getCollection();

    /**
     * Return new documenttype
     *
     * @return Documenttype
     */
    public function create();

    /**
     * Find document type
     *
     * @param string $key
     *
     * @return Documenttype
     * @throws NotFoundException
     */
    public function find($key);

    /**
     * Return document type by filename
     *
     * @param string $filename
     *
     * @return Documenttype
     */
    public function findByFilename($filename);

    /**
     * Return document type by mimetype
     *
     * @param string $mimetype
     *
     * @return Documenttype
     * @throws NotFoundException
     */
    public function findByMimetype($mimetype);

    /**
     * Return document type key by mimetype
     *
     * @param string $mimetype
     *
     * @return string
     */
    public function findKeyByMimetype($mimetype);

    /**
     * Return all document types
     *
     * @return Documenttype[]
     */
    public function findAll();
}