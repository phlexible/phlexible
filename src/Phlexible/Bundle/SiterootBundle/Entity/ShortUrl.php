<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Siteroot short url
 *
 * @author Phillip Look <plook@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="siteroot_shorturl")
 */
class ShortUrl
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hostname;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, options={"fixed"=true})
     */
    private $language;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $target;

    /**
     * @var Siteroot
     * @ORM\ManyToOne(targetEntity="Siteroot", inversedBy="shortUrls")
     * @ORM\JoinColumn(name="siteroot_id", referencedColumnName="id")
     */
    private $siteroot;

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
     * @return Siteroot
     */
    public function getSiteroot()
    {
        return $this->siteroot;
    }

    /**
     * @param Siteroot $siteroot
     *
     * @return $this
     */
    public function setSiteroot(Siteroot $siteroot = null)
    {
        $this->siteroot = $siteroot;

        return $this;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param string $hostname
     *
     * @return $this
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

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
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     *
     * @return $this
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }
}
