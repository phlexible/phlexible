<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Teaser online
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="teaser_online")
 */
class TeaserOnline
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Teaser
     * @ORM\ManyToOne(targetEntity="Teaser")
     * @ORM\JoinColumn(name="teaser_id", referencedColumnName="id")
     */
    private $teaser;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, options={"fixed"=true})
     */
    private $language;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $hash;

    /**
     * @var string
     * @ORM\Column(name="publish_user_id", type="string", length=36, options={"fixed"=true})
     */
    private $publishUserId;

    /**
     * @var \DateTime
     * @ORM\Column(name="published_at", type="datetime")
     */
    private $publishedAt;

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
     * @return Teaser
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * @param Teaser $teaser
     *
     * @return $this
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;

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
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     *
     * @return $this
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublishUserId()
    {
        return $this->publishUserId;
    }

    /**
     * @param string $publishUserId
     *
     * @return $this
     */
    public function setPublishUserId($publishUserId)
    {
        $this->publishUserId = $publishUserId;

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
}
