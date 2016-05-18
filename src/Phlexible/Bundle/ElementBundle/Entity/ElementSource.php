<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;

/**
 * Element source
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="element_source", indexes={@ORM\Index(columns={"elementtype_id"})})
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
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $xml;

    /**
     * @var Elementtype
     * @ORM\Column(type="object", nullable=true)
     */
    private $elementtype;

    /**
     * @var \DateTime
     * @ORM\Column(name="imported_at", type="datetime")
     */
    private $importedAt;

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
        return $this->importedAt;
    }

    /**
     * @param \DateTime $importedAt
     *
     * @return $this
     */
    public function setImportedAt(\DateTime $importedAt)
    {
        $this->importedAt = $importedAt;

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
     * @param Elementtype $elementtype
     *
     * @return $this
     */
    public function setElementtype(Elementtype $elementtype)
    {
        $this->elementtype = $elementtype;

        return $this;
    }

    /**
     * @return Elementtype
     */
    public function getElementtype()
    {
        return $this->elementtype;
    }
}
