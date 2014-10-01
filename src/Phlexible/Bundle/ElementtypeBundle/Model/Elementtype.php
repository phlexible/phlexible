<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Component\Identifier\IdentifiableInterface;

/**
 * Elementtype
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Elementtype implements IdentifiableInterface
{
    const TYPE_FULL = 'full';
    const TYPE_STRUCTURE = 'structure';
    const TYPE_LAYOUTAREA = 'layout';
    const TYPE_LAYOUTCONTAINER = 'layoutcontainer';
    const TYPE_PART = 'part';
    const TYPE_REFERENCE = 'reference';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $uniqueId;

    /**
     * @var int
     */
    private $revision;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $titles;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var int
     */
    private $defaultTab = 0;

    /**
     * @var bool
     */
    private $hideChildren = false;

    /**
     * @var bool
     */
    private $deleted = false;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var int
     */
    private $defaultContentTab;

    /**
     * @var string
     */
    private $metaSetId;

    /**
     * @var array
     */
    private $mappings = array();

    /**
     * @var ElementtypeStructure
     */
    private $structure;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var string
     */
    private $createUserId;

    /**
     * @var \DateTime
     */
    private $modifiedAt;

    /**
     * @var string
     */
    private $modifyUserId;

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
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
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
     * @return int
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * @param int $revision
     *
     * @return $this
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;

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
     * @param string $language
     *
     * @return string
     */
    public function getTitle($language = null)
    {
        if (!isset($this->titles[$language])) {
            return current($this->titles);
        }

        return $this->titles[$language];
    }

    /**
     * @param string $language
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($language, $title)
    {
        $this->titles[$language] = $title;

        return $this;
    }

    /**
     * Return element type icon
     *
     * @return string
     */
    public function getIcon()
    {
        if (!$this->icon) {
            return '_fallback.gif';
        }

        return $this->icon;
    }

    /**
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
     * Return element type default tab
     *
     * @return string
     */
    public function getDefaultTab()
    {
        return $this->defaultTab;
    }

    /**
     * @param int $defaultTab
     *
     * @return $this
     */
    public function setDefaultTab($defaultTab)
    {
        $this->defaultTab = isset($defaultTab) ? (int) $defaultTab : null;

        return $this;
    }

    /**
     * @return bool
     */
    public function getHideChildren()
    {
        return $this->hideChildren;
    }

    /**
     * @param bool $hideChildren
     *
     * @return $this
     */
    public function setHideChildren($hideChildren)
    {
        $this->hideChildren = (bool) $hideChildren;

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
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param \DateTime $modifiedAt
     *
     * @return $this
     */
    public function setModifiedAt(\DateTime $modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getModifyUserId()
    {
        return $this->modifyUserId;
    }

    /**
     * @param string $modifyUserId
     *
     * @return $this
     */
    public function setModifyUserId($modifyUserId)
    {
        $this->modifyUserId = $modifyUserId;

        return $this;
    }

    /**
     * @return ElementtypeIdentifier
     */
    public function getIdentifier()
    {
        return new ElementtypeIdentifier($this->getId());
    }

    /**
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     *
     * @return $this
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return ElementtypeStructure
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @param ElementtypeStructure $structure
     *
     * @return $this
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * Return element type default content tab
     *
     * @return string
     */
    public function getDefaultContentTab()
    {
        return $this->defaultContentTab;
    }

    /**
     * @param int $defaultContentTab
     *
     * @return $this
     */
    public function setDefaultContentTab($defaultContentTab)
    {
        $this->defaultContentTab = isset($defaultContentTab) ? (int) $defaultContentTab : null;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaSetId()
    {
        return $this->metaSetId;
    }

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function setMetaSetId($metaSetId)
    {
        $this->metaSetId = $metaSetId;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return array
     */
    public function getMappings()
    {
        return $this->mappings;
    }

    /**
     * @param array $mappings
     *
     * @return $this
     */
    public function setMappings(array $mappings = null)
    {
        $this->mappings = $mappings;

        return $this;
    }

}
