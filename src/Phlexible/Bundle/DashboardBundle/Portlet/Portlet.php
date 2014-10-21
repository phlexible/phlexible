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
class Portlet
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
    private $role;

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
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
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
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getIconClass()
    {
        return $this->iconClass;
    }

    /**
     * @param string $iconClass
     *
     * @return $this
     */
    public function setIconClass($iconClass)
    {
        $this->iconClass = $iconClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasRole()
    {
        return $this->role !== null;
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
