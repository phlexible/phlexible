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
        if (!is_array($mimetypes)) {
            print_r($this);die;
        }

        return $this;
    }

    /**
     * @param string $mimetype
     */
    public function addMimetype($mimetype)
    {
        $this->mimetypes[] = $mimetype;
    }

    /**
     * Is an icon for the given size available?
     *
     * @param int $neededSize
     *
     * @return bool
     */
    public function hasIcon($neededSize = 16)
    {
        $documentTypeKey = $this->key;

        $sizes    = array(-1, 16, 32, 48, 256);
        $imgDir  = __DIR__ . '/../Resources/public/mimetypes';

        if ($documentTypeKey !== null) {
            $imgFile = $documentTypeKey . '.gif';
        } else {
            $imgFile = '_fallback.gif';
        }

        $i = count($sizes) - 1;
        $size = $sizes[$i];

        while (!empty($sizes[$i - 1]) &&
            ($sizes[$i] > $neededSize || !file_exists($imgDir . $sizes[$i] . '/' . $imgFile))) {
            $size = $sizes[--$i];
        }

        return $size !== -1;
    }

    /**
     * Return icon
     *
     * @param int $neededSize
     *
     * @return string
     */
    public function getIcon($neededSize = 16)
    {
        $documentTypeKey = $this->key;

        $sizes    = array(-1, 16, 32, 48, 256);
        $imgDir  = __DIR__ . '/../Resources/public/mimetypes';

        if ($documentTypeKey !== null) {
            $imgFile = $documentTypeKey . '.gif';
        } else {
            $imgFile = '_fallback.gif';
        }

        $i = count($sizes) - 1;
        $size = $sizes[$i];

        if (!is_null($neededSize)) {
            while (!empty($sizes[$i - 1]) &&
                ($sizes[$i] > $neededSize || !file_exists($imgDir . $sizes[$i] . '/' . $imgFile))) {
                $size = $sizes[--$i];
            }
        }

        if ($size == -1) {
            $i = count($sizes) - 1;
            $size = $sizes[$i];

            while (!empty($sizes[$i - 1]) &&
                ($sizes[$i] > $neededSize || !file_exists($imgDir . $sizes[$i] . '/_fallback.gif'))) {
                $size = $sizes[--$i];
            }

            return $imgDir . $size . '/_fallback.gif';
        }

        return $imgDir . $size . '/' . $imgFile;
    }
}
