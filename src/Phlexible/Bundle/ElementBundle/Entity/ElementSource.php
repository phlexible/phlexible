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
     * @ORM\Column(type="string")
     */
    private $name;

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
     * @ORM\Column(type="string", nullable=true)
     */
    private $template;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $icon;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $defaultTab;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $defaultContentTab;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $hideChildren;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $noIndex;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $metaSetId;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $xml;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultTab()
    {
        return $this->defaultTab;
    }

    /**
     * @param int $defaultTab
     *
     * @return $this
     */
    public function setDefaultTab($defaultTab)
    {
        $this->defaultTab = $defaultTab;

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultContentTab()
    {
        return $this->defaultContentTab;
    }

    /**
     * @param int $defaultContentTab
     *
     * @return $this
     */
    public function setDefaultContentTab($defaultContentTab)
    {
        $this->defaultContentTab = $defaultContentTab;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getHideChildren()
    {
        return $this->hideChildren;
    }

    /**
     * @param boolean $hideChildren
     *
     * @return $this
     */
    public function setHideChildren($hideChildren)
    {
        $this->hideChildren = $hideChildren;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

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
     * @return boolean
     */
    public function getNoIndex()
    {
        return $this->noIndex;
    }

    /**
     * @param boolean $noIndex
     *
     * @return $this
     */
    public function setNoIndex($noIndex)
    {
        $this->noIndex = $noIndex;

        return $this;
    }
}
