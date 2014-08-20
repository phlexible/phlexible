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
 * Siteroot url
 *
 * @author Phillip Look <plook@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="siteroot_url")
 */
class Url
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    private $id;

    /**
     * @var bool
     * @ORM\Column(name="is_global_default", type="boolean")
     */
    private $globalDefault;

    /**
     * @var bool
     * @ORM\Column(name="is_default", type="boolean")
     */
    private $default;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $hostname;

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
     * @ORM\ManyToOne(targetEntity="Siteroot", inversedBy="urls")
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
    public function setSiteroot(Siteroot $siteroot)
    {
        $this->siteroot = $siteroot;

        return $this;
    }

    /**
     * @return bool
     */
    public function isGlobalDefault()
    {
        return $this->globalDefault;
    }

    /**
     * @param bool $globalDefault
     *
     * @return $this
     */
    public function setGlobalDefault($globalDefault)
    {
        $this->globalDefault = (bool) $globalDefault;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @param bool $default
     *
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = (bool) $default;

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
     * @param string $url
     *
     * @return $this
     */
    public function setHostname($url)
    {
        $this->hostname = $url;

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
