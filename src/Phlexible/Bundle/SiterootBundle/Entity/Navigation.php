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
 * Siteroot navigation
 *
 * @author Phillip Look <plook@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="siteroot_navigation")
 */
class Navigation
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
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $handler;

    /**
     * @var int
     * @ORM\Column(name="start_tree_id", type="integer")
     */
    private $startTreeId;

    /**
     * @var int
     * @ORM\Column(name="max_depth", type="integer")
     */
    private $maxDepth;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $flags;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $additional;

    /**
     * @var Siteroot
     * @ORM\ManyToOne(targetEntity="Siteroot", inversedBy="navigations")
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
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param string $handler
     *
     * @return $this
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * @return int
     */
    public function getStartTreeId()
    {
        return $this->startTreeId;
    }

    /**
     * @param int $startTid
     *
     * @return $this
     */
    public function setStartTreeId($startTid)
    {
        $this->startTreeId = $startTid;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    /**
     * @param int $maxDepth
     *
     * @return $this
     */
    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth;

        return $this;
    }

    /**
     * @return int
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * @param int $flags
     *
     * @return $this
     */
    public function setFlags($flags)
    {
        $this->flags = $flags;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditional()
    {
        return $this->additional;
    }

    /**
     * @param string $additional
     *
     * @return $this
     */
    public function setAdditional($additional)
    {
        $this->additional = $additional;

        return $this;
    }
}
