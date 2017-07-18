<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Teaser.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="teaser")
 */
class Teaser
{
    /**
     * Node Type: element.
     */
    const TYPE_ELEMENT = 'element';

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="tree_id", type="integer")
     */
    private $treeId;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $eid;

    /**
     * @var string
     * @ORM\Column(name="layoutarea_id", type="string", length=36, options={"fixed"=true})
     */
    private $layoutareaId;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $type;

    /**
     * @var int
     * @ORM\Column(name="type_id", type="integer", nullable=true)
     */
    private $typeId;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $sort;

    /**
     * @var array
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $attributes;

    /**
     * @var string
     * @ORM\Column(name="create_user_id", type="string", length=36, options={"fixed"=true})
     */
    private $createUserId;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var bool
     */
    private $hidden = false;

    /**
     * @var bool
     */
    private $stopped = false;

    /**
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
     * @return int
     */
    public function getTreeId()
    {
        return $this->treeId;
    }

    /**
     * @param int $treeId
     *
     * @return $this
     */
    public function setTreeId($treeId)
    {
        $this->treeId = $treeId;

        return $this;
    }

    /**
     * @return int
     */
    public function getEid()
    {
        return $this->eid;
    }

    /**
     * @param int $eid
     *
     * @return $this
     */
    public function setEid($eid)
    {
        $this->eid = $eid;

        return $this;
    }

    /**
     * @return string
     */
    public function getLayoutareaId()
    {
        return $this->layoutareaId;
    }

    /**
     * @param string $layoutareaId
     *
     * @return $this
     */
    public function setLayoutareaId($layoutareaId)
    {
        $this->layoutareaId = $layoutareaId;

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
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param int $typeId
     *
     * @return $this
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     *
     * @return $this
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @param int $nodeId
     *
     * @return int
     */
    public function getInheritSort($nodeId)
    {
        if (isset($this->attributes['inheritSort'][$nodeId])) {
            return $this->attributes['inheritSort'][$nodeId];
        }

        return $this->sort;
    }

    /**
     * @param int $nodeId
     * @param int $sort
     *
     * @return $this
     */
    public function setInheritSort($nodeId, $sort)
    {
        if (!isset($this->attributes['inheritSort'])) {
            $this->attributes['inheritSort'] = [];
        }

        $this->attributes['inheritSort'][$nodeId] = $sort;

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getAttribute($key, $default = null)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return $default;
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function removeAttribute($key)
    {
        if (isset($this->attributes[$key])) {
            unset($this->attributes[$key]);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getCreateUserId()
    {
        return $this->createUserId;
    }

    /**
     * @param string $createUserId
     *
     * @return $this
     */
    public function setCreateUserId($createUserId)
    {
        $this->createUserId = $createUserId;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return array
     */
    public function getStopIds()
    {
        return $this->getAttribute('stopIds', array());
    }

    /**
     * @param array $stopIds
     *
     * @return $this
     */
    public function setStopIds($stopIds = array())
    {
        $this->setAttribute('stopIds', $stopIds);

        return $this;
    }

    /**
     * @param int $stopId
     *
     * @return $this
     */
    public function addStopId($stopId)
    {
        $stopIds = $this->getStopIds();

        if (!in_array($stopId, $stopIds)) {
            $stopIds[] = $stopId;
            $this->setStopIds($stopIds);
        }

        return $this;
    }

    /**
     * @param int $stopId
     *
     * @return $this
     */
    public function removeStopId($stopId)
    {
        $stopIds = $this->getStopIds();

        if (in_array($stopId, $stopIds)) {
            unset($stopIds[array_search($stopId, $stopIds)]);
            $this->setStopIds($stopIds);
        }

        return $this;
    }

    /**
     * @param int $stopId
     *
     * @return bool
     */
    public function hasStopId($stopId)
    {
        $stopIds = $this->getStopIds();

        return in_array($stopId, $stopIds);
    }

    /**
     * @return array
     */
    public function getHideIds()
    {
        return $this->getAttribute('hideIds', array());
    }

    /**
     * @param array $hideIds
     *
     * @return $this
     */
    public function setHideIds($hideIds)
    {
        return $this->setAttribute('hideIds', $hideIds);
    }

    /**
     * @param int $hideId
     *
     * @return $this
     */
    public function addHideId($hideId)
    {
        $hideIds = $this->getHideIds();

        if (!in_array($hideId, $hideIds)) {
            $hideIds[] = $hideId;
            $this->setHideIds($hideIds);
        }

        return $this;
    }

    /**
     * @param int $hideId
     *
     * @return $this
     */
    public function removeHideId($hideId)
    {
        $hideIds = $this->getHideIds();

        if (in_array($hideId, $hideIds)) {
            unset($hideIds[array_search($hideId, $hideIds)]);
            $this->setHideIds($hideIds);
        }

        return $this;
    }

    /**
     * @param int $hideId
     *
     * @return bool
     */
    public function hasHideId($hideId)
    {
        $hideIds = $this->getHideIds();

        return in_array($hideId, $hideIds);
    }

    public function getCache()
    {
        return $this->getAttribute('cache', []);
    }

    public function setCache($cache)
    {
        return $this->setAttribute('cache', $cache);
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->getAttribute('controller');
    }

    /**
     * @param string $controller
     *
     * @return $this
     */
    public function setController($controller)
    {
        return $this->setAttribute('controller', $controller);
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->getAttribute('template');
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        return $this->setAttribute('template', $template);
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     *
     * @return $this
     */
    public function setHidden($hidden = true)
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStopped()
    {
        return $this->stopped;
    }

    /**
     * @param bool $stopped
     *
     * @return $this
     */
    public function setStopped($stopped = true)
    {
        $this->stopped = $stopped;

        return $this;
    }
}
