<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Component\Identifier\IdentifiableInterface;

/**
 * Elementtype
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="elementtype")
 */
class Elementtype implements IdentifiableInterface
{
    const TYPE_FULL            = 'full';
    const TYPE_STRUCTURE       = 'structure';
    const TYPE_LAYOUTAREA      = 'layout';
    const TYPE_LAYOUTCONTAINER = 'layoutcontainer';
    const TYPE_PART            = 'part';
    const TYPE_REFERENCE       = 'reference';

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue("AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="unique_id", type="string", length=255, unique=true)
     */
    private $uniqueId;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icon;

    /**
     * @var int
     * @ORM\Column(name="default_tab", type="integer", nullable=true)
     */
    private $defaultTab;

    /**
     * @var bool
     * @ORM\Column(name="hide_children", type="boolean")
     */
    private $hideChildren;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    private $deleted = false;

    /**
     * @var int
     * @ORM\Column(name="latest_version", type="integer")
     */
    private $latestVersion = 0;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(name="create_user_id", type="string", length=36, options={"fixed"=true})
     */
    private $createUserId;

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
        $this->id = (int) $id;

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
     * @return int
     */
    public function getLatestVersion()
    {
        return $this->latestVersion;
    }

    /**
     * @param int $latestVersion
     *
     * @return $this
     */
    public function setLatestVersion($latestVersion)
    {
        $this->latestVersion = $latestVersion;

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
     * @return ElementtypeIdentifier
     */
    public function getIdentifier()
    {
        return new ElementtypeIdentifier($this->getId());
    }
}
