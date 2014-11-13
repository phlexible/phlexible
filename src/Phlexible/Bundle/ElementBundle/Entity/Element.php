<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Element
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity(repositoryClass="Phlexible\Bundle\ElementBundle\Entity\Repository\ElementRepository")
 * @ORM\Table(name="element")
 */
class Element
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $eid;

    /**
     * @var string
     * @ORM\Column(name="unique_id", type="string", length=255, nullable=true, unique=true)
     */
    private $uniqueId;

    /**
     * @var string
     * @ORM\Column(name="elementtype_id", type="string")
     * @TODO: elementSource?
     */
    private $elementtypeId;

    /**
     * @var string
     * @ORM\Column(name="master_language", type="string", length=2, options={"fixed"=true})
     */
    private $masterLanguage;

    /**
     * @var int
     * @ORM\Column(name="latest_version", type="integer")
     */
    private $latestVersion;

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
    public function getElementtypeId()
    {
        return $this->elementtypeId;
    }

    /**
     * @param string $elementtypeId
     *
     * @return $this
     */
    public function setElementtypeId($elementtypeId)
    {
        $this->elementtypeId = $elementtypeId;

        return $this;
    }

    /**
     * @return string
     */
    public function getMasterLanguage()
    {
        return $this->masterLanguage;
    }

    /**
     * @param string $masterLanguage
     *
     * @return $this
     */
    public function setMasterLanguage($masterLanguage)
    {
        $this->masterLanguage = $masterLanguage;

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
}
