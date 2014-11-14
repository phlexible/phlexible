<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\Model;

/**
 * Documenttype
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Documenttype
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $titles = array();

    /**
     * @var array
     */
    private $mimetypes = array();

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function getTitles()
    {
        return $this->titles;
    }

    /**
     * @param array $titles
     *
     * @return $this
     */
    public function setTitles(array $titles)
    {
        $this->titles = $titles;

        return $this;
    }

    /**
     * Return localized title
     *
     * @param string $code
     *
     * @return string
     */
    public function getTitle($code)
    {
        if (!isset($this->titles[$code])) {
            $code = key($this->getTitles());
        }

        return $this->titles[$code];
    }

    /**
     * Set title
     *
     * @param string $code
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($code, $title)
    {
        $this->titles[$code] = $title;

        return $this;
    }

    /**
     * @return array
     */
    public function getMimetypes()
    {
        return $this->mimetypes;
    }

    /**
     * @param array $mimetypes
     *
     * @return $this
     */
    public function setMimetypes($mimetypes)
    {
        $this->mimetypes = $mimetypes;

        return $this;
    }

    /**
     * @param string $mimetype
     */
    public function addMimetype($mimetype)
    {
        $this->mimetypes[] = $mimetype;
    }
}
