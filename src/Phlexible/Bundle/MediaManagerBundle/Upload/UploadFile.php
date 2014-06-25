<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Upload;

/**
 * Upload file
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UploadFile
{
    /**
     * @var string
     */
    private $tempName;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var integer
     */
    private $error;

    /**
     * @param string  $tempName
     * @param string  $name
     * @param string  $type
     * @param integer $size
     * @param integer $error
     */
    public function __construct($tempName, $name, $type, $size, $error)
    {
        $this->tempName = $tempName;
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getTempName()
    {
        return $this->tempName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return integer
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return boolean
     */
    public function hasError()
    {
        return $this->error > 1;
    }
}
