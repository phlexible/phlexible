<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TemplateBundle;

/**
 * Template identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Template
{
    /**
     * @var string
     */
    private $id = null;

    /**
     * @var string
     */
    private $name = null;

    /**
     * @var string
     */
    private $path = null;

    /**
     * @var string
     */
    private $filename = null;

    /**
     * @var string
     */
    private $absoluteFilename = null;

    /**
     * Return template ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Return template name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set template name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Return template path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set template path
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Return template filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set template filename
     *
     * @param string $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Return template absolute filename
     *
     * @return string
     */
    public function getAbsoluteFilename()
    {
        return $this->absoluteFilename;
    }

    /**
     * Set template absolute filename
     *
     * @param string $absoluteFilename
     * @return $this
     */
    public function setAbsoluteFilename($absoluteFilename)
    {
        $this->absoluteFilename = $absoluteFilename;
        return $this;
    }

    /**
     * Return template content
     *
     * @return string
     */
    public function getContent()
    {
        return file_get_contents($this->getAbsoluteFilename());
    }
}
