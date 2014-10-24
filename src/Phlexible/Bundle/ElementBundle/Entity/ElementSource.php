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
 * Element source
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="element_source")
 */
class ElementSource
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
     * @ORM\Column(name="elementtype_id", type="string")
     */
    private $elementtypeId;

    /**
     * @var int
     * @ORM\Column(name="elementtype_revision", type="integer")
     */
    private $elementtypeRevision;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $xml;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
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
     * @return int
     */
    public function getElementtypeRevision()
    {
        return $this->elementtypeRevision;
    }

    /**
     * @param int $elementtypeRevision
     *
     * @return $this
     */
    public function setElementtypeRevision($elementtypeRevision)
    {
        $this->elementtypeRevision = $elementtypeRevision;

        return $this;
    }

    /**
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * @param string $xml
     *
     * @return $this
     */
    public function setXml($xml)
    {
        $this->xml = $xml;

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
}