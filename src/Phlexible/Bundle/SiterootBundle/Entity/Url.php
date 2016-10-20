<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
    private $globalDefault = false;

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
     * @ORM\JoinColumn(name="siteroot_id", referencedColumnName="id", onDelete="CASCADE")
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
    public function setGlobalDefault($globalDefault = true)
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
