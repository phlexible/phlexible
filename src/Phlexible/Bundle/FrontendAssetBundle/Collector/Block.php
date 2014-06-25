<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendAssetBundle\Collector;

/**
 * Block
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Block
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $files = array();

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Append file
     *
     * @param string $file
     *
     * @return $this
     */
    public function append($file)
    {
        if (!in_array($file, $this->files)) {
            array_push($this->files, $file);
        }

        return $this;
    }

    /**
     * Prepend file
     *
     * @param string $file
     *
     * @return $this
     */
    public function prepend($file)
    {
        if (!in_array($file, $this->files)) {
            array_unshift($this->files, $file);
        }

        return $this;
    }
}
