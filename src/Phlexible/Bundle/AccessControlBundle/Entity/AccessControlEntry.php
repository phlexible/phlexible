<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Access control entry
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="access_control", uniqueConstraints={@ORM\UniqueConstraint(columns={"content_type", "content_id", "security_type", "security_id", "content_language"})})
 */
class AccessControlEntry
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="content_type", type="string", length=100)
     */
    private $contentType;

    /**
     * @var string
     * @ORM\Column(name="content_id", type="string", length=100)
     */
    private $contentId;

    /**
     * @var string
     * @ORM\Column(name="security_type", type="string", length=100)
     */
    private $securityType;

    /**
     * @var string
     * @ORM\Column(name="security_id", type="string", length=100)
     */
    private $securityId;

    /**
     * @var string
     * @ORM\Column(name="content_language", type="string", length=2, nullable=true, options={"fixed"=true})
     */
    private $contentLanguage;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=100)
     */
    private $type;

    /**
     * @var int
     * @ORM\Column(name="mask", type="integer", options={"default"=0})
     */
    private $mask;

    /**
     * @var int
     * @ORM\Column(name="stop_mask", type="integer", options={"default"=0})
     */
    private $stopMask;

    /**
     * @var int
     * @ORM\Column(name="no_inherit_mask", type="integer", options={"default"=0})
     */
    private $noInheritMask;

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
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * @param string $contentId
     *
     * @return $this
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentLanguage()
    {
        return $this->contentLanguage;
    }

    /**
     * @param string $contentLanguage
     *
     * @return $this
     */
    public function setContentLanguage($contentLanguage)
    {
        $this->contentLanguage = $contentLanguage;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecurityType()
    {
        return $this->securityType;
    }

    /**
     * @param string $securityType
     *
     * @return $this
     */
    public function setSecurityType($securityType)
    {
        $this->securityType = $securityType;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecurityId()
    {
        return $this->securityId;
    }

    /**
     * @param string $securityId
     *
     * @return $this
     */
    public function setSecurityId($securityId)
    {
        $this->securityId = $securityId;

        return $this;
    }

    /**
     * @return int
     */
    public function getMask()
    {
        return $this->mask;
    }

    /**
     * @param int $mask
     *
     * @return $this
     */
    public function setMask($mask)
    {
        $this->mask = $mask;

        return $this;
    }

    /**
     * @return int
     */
    public function getStopMask()
    {
        return $this->stopMask;
    }

    /**
     * @param int $stopMask
     *
     * @return $this
     */
    public function setStopMask($stopMask)
    {
        $this->stopMask = $stopMask;

        return $this;
    }

    /**
     * @return int
     */
    public function getNoInheritMask()
    {
        return $this->noInheritMask;
    }

    /**
     * @param int $inheritMask
     *
     * @return $this
     */
    public function setNoInheritMask($inheritMask)
    {
        $this->noInheritMask = $inheritMask;

        return $this;
    }
}
