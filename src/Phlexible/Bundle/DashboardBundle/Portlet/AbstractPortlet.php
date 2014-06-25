<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DashboardBundle\Portlet;

/**
 * Portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractPortlet
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $iconClass;

    /**
     * @var string
     */
    private $resource;

    /**
     * @var array
     */
    private $data = array();

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getIconClass()
    {
        return $this->iconClass;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return boolean
     */
    public function hasResource()
    {
        return $this->resource !== null;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return array();
    }

    /**
     * Return array representation of this portlet
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'       => $this->getId(),
            'title'    => $this->getTitle(),
            'class'    => $this->getClass(),
            'iconCls'  => $this->getIconClass(),
            'data'     => $this->getData(),
            'settings' => $this->getSettings(),
        );
    }

}
