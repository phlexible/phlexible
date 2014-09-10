<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Element finder lookup element
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="catch_lookup_element")
 */
class ElementFinderLookupElement
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $eid;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var int
     * @ORM\Column(name="tree_id", type="integer")
     */
    private $treeId;

    /**
     * @var int
     * @ORM\Column(name="elementtype_id", type="integer")
     */
    private $elementtypeId;

    /**
     * @var bool
     * @ORM\Column(name="is_preview", type="boolean")
     */
    private $isPreview;

    /**
     * @var bool
     * @ORM\Column(name="in_navigation", type="boolean")
     */
    private $inNavigation;

    /**
     * @var bool
     * @ORM\Column(name="is_restricted", type="boolean")
     */
    private $isRestricted;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, options={"fixed"=true})
     */
    private $language;

    /**
     * @var int
     * @ORM\Column(name="online_version", type="integer", nullable=true)
     */
    private $onlineVersion;

    /**
     * @var \DateTime
     * @ORM\Column(name="published_at", type="datetime")
     */
    private $publishedAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="custom_date", type="datetime", nullable=true)
     */
    private $customDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="cached_at", type="datetime")
     */
    private $cachedAt;

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
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;

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
    public function getElementtypeId()
    {
        return $this->elementtypeId;
    }

    /**
     * @param int $elementtypeId
     *
     * @return $this
     */
    public function setElementtypeId($elementtypeId)
    {
        $this->elementtypeId = $elementtypeId;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsPreview()
    {
        return $this->isPreview;
    }

    /**
     * @param boolean $isPreview
     *
     * @return $this
     */
    public function setIsPreview($isPreview)
    {
        $this->isPreview = $isPreview;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getInNavigation()
    {
        return $this->inNavigation;
    }

    /**
     * @param boolean $inNavigation
     *
     * @return $this
     */
    public function setInNavigation($inNavigation)
    {
        $this->inNavigation = $inNavigation;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsRestricted()
    {
        return $this->isRestricted;
    }

    /**
     * @param boolean $isRestricted
     *
     * @return $this
     */
    public function setIsRestricted($isRestricted)
    {
        $this->isRestricted = $isRestricted;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return int
     */
    public function getOnlineVersion()
    {
        return $this->onlineVersion;
    }

    /**
     * @param int $onlineVersion
     *
     * @return $this
     */
    public function setOnlineVersion($onlineVersion)
    {
        $this->onlineVersion = $onlineVersion;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     *
     * @return $this
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCustomDate()
    {
        return $this->customDate;
    }

    /**
     * @param \DateTime $customDate
     *
     * @return $this
     */
    public function setCustomDate($customDate)
    {
        $this->customDate = $customDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCachedAt()
    {
        return $this->cachedAt;
    }

    /**
     * @param \DateTime $cachedAt
     *
     * @return $this
     */
    public function setCachedAt($cachedAt)
    {
        $this->cachedAt = $cachedAt;

        return $this;
    }
}
