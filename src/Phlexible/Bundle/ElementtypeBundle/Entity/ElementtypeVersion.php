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
 * Elementtype version
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="elementtype_version")
 */
class ElementtypeVersion implements IdentifiableInterface
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue("AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Elementtype
     * @ORM\ManyToOne(targetEntity="Elementtype")
     * @ORM\JoinColumn(name="elementtype_id", referencedColumnName="id")
     */
    private $elementtype;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @var int
     * @ORM\Column(name="default_content_tab", type="integer", nullable=true)
     */
    private $defaultContentTab;

    /**
     * @var string
     * @ORM\Column(name="metaset_id", type="string", length=36, nullable=true, options={"fixed"=true})
     */
    private $metaSetId;

    /**
     * @var array
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $mappings = array();

    /**
     * @var string
     * @ORM\Column(name="create_user_id", type="string", length=36, options={"fixed"=true})
     */
    private $createUserId;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @return Elementtype
     */
    public function getElementtype()
    {
        return $this->elementtype;
    }

    /**
     * @param Elementtype $elementType
     *
     * @return $this
     */
    public function setElementtype(Elementtype $elementType)
    {
        $this->elementtype = $elementType;

        return $this;
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param integer $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = (integer) $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->elementtype->getType();
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->elementtype->getUniqueId();
    }

    /**
     * Return element type default content tab
     *
     * @return string
     */
    public function getDefaultContentTab()
    {
        return $this->defaultContentTab;
    }

    /**
     * @param integer $defaultContentTab
     *
     * @return $this
     */
    public function setDefaultContentTab($defaultContentTab)
    {
        $this->defaultContentTab = isset($defaultContentTab) ? (int) $defaultContentTab : null;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaSetId()
    {
        return $this->metaSetId;
    }

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function setMetaSetId($metaSetId)
    {
        $this->metaSetId = $metaSetId;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return array
     */
    public function getMappings()
    {
        return $this->mappings;
    }

    /**
     * @param array $mappings
     *
     * @return $this
     */
    public function setMappings(array $mappings = null)
    {
        $this->mappings = $mappings;

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
     * @return ElementtypeVersionIdentifier
     */
    public function getIdentifier()
    {
        return new ElementtypeVersionIdentifier(
            $this->getElementtype()->getId(),
            $this->version
        );
    }
}
