<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\Model;

/**
 * Documenttype collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DocumenttypeCollection
{
    /**
     * @var Documenttype[]
     */
    private $documenttypes = array();

    /**
     * @var array
     */
    private $mimetypeMap = array();

    /**
     * Add document type
     *
     * @param Documenttype $documenttype
     *
     * @return $this
     */
    public function add(Documenttype $documenttype)
    {
        $this->documenttypes[$documenttype->getKey()] = $documenttype;

        foreach ($documenttype->getMimetypes() as $mimetype) {
            $this->mimetypeMap[$mimetype] = $documenttype->getKey();
        }

        return $this;
    }

    /**
     * Merge collection
     *
     * @param DocumenttypeCollection $collection
     *
     * @return $this
     */
    public function merge(DocumenttypeCollection $collection)
    {
        foreach ($collection->getAll() as $documenttype) {
            $this->add($documenttype);
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return Documenttype
     */
    public function get($key)
    {
        if (isset($this->documenttypes[$key])) {
            return $this->documenttypes[$key];
        }

        return null;
    }

    /**
     * @param string $mimetype
     *
     * @return Documenttype
     */
    public function getByMimetype($mimetype)
    {
        if (isset($this->mimetypeMap[$mimetype])) {
            return $this->get($this->mimetypeMap[$mimetype]);
        }

        return null;
    }

    /**
     * @return Documenttype[]
     */
    public function getAll()
    {
        return $this->documenttypes;
    }

    /**
     * @return Documenttype
     */
    public function create()
    {
        return new Documenttype();
    }

    /**
     * @return string
     */
    public function getHash()
    {
        $base = '';
        foreach ($this->documenttypes as $documenttype) {
            $base .= md5(serialize($documenttype));
        }

        return md5($base);
    }
}
