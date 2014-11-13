<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Media cache queue item
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity(repositoryClass = "Phlexible\Bundle\MediaCacheBundle\Entity\Repository\QueueItemRepository")
 * @ORM\Table(name="media_cache_queue")
 */
class QueueItem
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=32, options={"fixed"=true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="site_id", type="string", length=36, options={"fixed"=true})
     */
    private $siteId;

    /**
     * @var string
     * @ORM\Column(name="file_id", type="string", length=36, options={"fixed"=true})
     */
    private $fileId;

    /**
     * @var int
     * @ORM\Column(name="file_version", type="integer")
     */
    private $fileVersion = 1;

    /**
     * @var string
     * @ORM\Column(name="template_key", type="string", length=100)
     */
    private $templateKey;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

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
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param string $siteId
     *
     * @return $this
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * @param string $fileId
     *
     * @return $this
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;

        return $this;
    }

    /**
     * @return int
     */
    public function getFileVersion()
    {
        return $this->fileVersion;
    }

    /**
     * @param int $fileVersion
     *
     * @return $this
     */
    public function setFileVersion($fileVersion)
    {
        $this->fileVersion = $fileVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateKey()
    {
        return $this->templateKey;
    }

    /**
     * @param string $templateKey
     *
     * @return $this
     */
    public function setTemplateKey($templateKey)
    {
        $this->templateKey = $templateKey;

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
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
