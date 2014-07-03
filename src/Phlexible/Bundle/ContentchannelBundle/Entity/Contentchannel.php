<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ContentchannelBundle\Entity;

/**
 * Content channel
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Contentchannel
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $uniqueId;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var string
     */
    private $templateFolder;

    /**
     * @var string
     */
    private $rendererClassname;

    /**
     * Return ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Return title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Return unique ID
     *
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * Set unique ID
     *
     * @param string $uniqueId
     *
     * @return $this
     */
    public function setUniqueId($uniqueId)
    {
        $this->uniqueId = $uniqueId;

        return $this;
    }

    /**
     * Return icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Return template folder
     *
     * @return string
     */
    public function getTemplateFolder()
    {
        return $this->templateFolder;
    }

    /**
     * Set template folder
     *
     * @param string $templateFolder
     *
     * @return $this
     */
    public function setTemplateFolder($templateFolder)
    {
        $this->templateFolder = $templateFolder;

        return $this;
    }

    /**
     * Return renderer classname
     *
     * @return string
     */
    public function getRendererClassname()
    {
        return $this->rendererClassname;
    }

    /**
     * Set renderer classname
     *
     * @param string $rendererClassname
     *
     * @return $this
     */
    public function setRendererClassname($rendererClassname)
    {
        $this->rendererClassname = $rendererClassname;

        return $this;
    }
}
