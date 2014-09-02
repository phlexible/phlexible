<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Component\Identifier\IdentifiableInterface;
use Phlexible\Bundle\TeaserBundle\Teaser\TeaserIdentifier;

/**
 * Teaser
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="teaser")
 */
class Teaser implements IdentifiableInterface
{
    const TYPE_TEASER    = 'teaser';
    const TYPE_CATCH     = 'catch';
    const TYPE_INHERITED = 'inherited';
    const TYPE_STOP      = 'stop';
    const TYPE_HIDE      = 'hide';

    /**
     * Node Type: element
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
     * @var int
     * @ORM\Column(name="layoutarea_id", type="integer")
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
     * @var string
     * @ORM\Column(name="template_id", type="string", length=36, nullable=true, options={"fixed"=true})
     */
    private $templateId;

    /**
     * @var bool
     * @ORM\Column(name="stop_inherit", type="boolean")
     */
    private $stopInherit = false;

    /**
     * @var bool
     * @ORM\Column(name="no_display", type="boolean")
     */
    private $noDisplay = false;

    /**
     * @var bool
     * @ORM\Column(name="disable_cache", type="boolean")
     */
    private $disableCache = false;

    /**
     * @var int
     * @ORM\Column(name="cache_lifetime", type="integer", nullable=true)
     */
    private $cacheLifeTime;

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
     * @return TeaserIdentifier
     */
    public function getIdentifier()
    {
        return new TeaserIdentifier($this->id);
    }

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
     * @return int
     */
    public function getLayoutareaId()
    {
        return $this->layoutareaId;
    }

    /**
     * @param int $layoutareaId
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
     * @return bool
     */
    public function isCacheDisabled()
    {
        return $this->disableCache;
    }

    /**
     * @param bool $cacheDisabled
     *
     * @return $this
     */
    public function setCacheDisabled($cacheDisabled = true)
    {
        $this->disableCache = $cacheDisabled;

        return $this;
    }

    /**
     * @return int
     */
    public function getCacheLifeTime()
    {
        return $this->cacheLifeTime;
    }

    /**
     * @param int $cacheLifeTime
     *
     * @return $this
     */
    public function setCacheLifeTime($cacheLifeTime)
    {
        $this->cacheLifeTime = $cacheLifeTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @param string $templateId
     *
     * @return $this
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;

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
     * @return boolean
     */
    public function getStopInherit()
    {
        return $this->stopInherit;
    }

    /**
     * @param boolean $stopInherit
     *
     * @return $this
     */
    public function setStopInherit($stopInherit)
    {
        $this->stopInherit = $stopInherit;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getNoDisplay()
    {
        return $this->noDisplay;
    }

    /**
     * @param boolean $noDisplay
     *
     * @return $this
     */
    public function setNoDisplay($noDisplay)
    {
        $this->noDisplay = $noDisplay;

        return $this;
    }
}
