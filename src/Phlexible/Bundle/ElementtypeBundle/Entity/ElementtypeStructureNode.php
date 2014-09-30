<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;

/**
 * Elementtype structure node
 *
 * @author Phillip Look <plook@brainbits.net>
 *
 * @ORM\Entity(repositoryClass="Phlexible\Bundle\ElementtypeBundle\Entity\Repository\ElementtypeStructureNodeRepository")
 * @ORM\Table(name="elementtype_structure")
 */
class ElementtypeStructureNode
{
    const FIELD_TYPE_REFERENCE = 'reference';
    const FIELD_TYPE_REFERENCE_ROOT = 'referenceroot';

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Elementtype
     * @ORM\ManyToOne(targetEntity="Elementtype")
     * @ORM\JoinColumn(name="elementtype_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $elementtype;

    /**
     * @var ElementtypeStructure
     */
    private $elementtypeStructure;

    /**
     * @var ElementtypeStructureNode
     * @ORM\ManyToOne(targetEntity="ElementtypeStructureNode")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parentNode;

    /**
     * @var int
     * @ORM\Column(name="elementtype_version", type="integer")
     */
    private $version;

    /**
     * @var string
     * @ORM\Column(name="ds_id", type="string", length=36, options={"fixed"=true})
     */
    private $dsId;

    /**
     * @var int
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * @var string
     * @ORM\Column(name="parent_ds_id", type="string", length=36, nullable=true, options={"fixed"=true})
     */
    private $parentDsId;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $sort;

    /**
     * @var Elementtype
     * @ORM\ManyToOne(targetEntity="Elementtype")
     * @ORM\JoinColumn(name="reference_id", referencedColumnName="id", nullable=true)
     */
    private $referenceElementtype;

    /**
     * @var int
     * @ORM\Column(name="reference_version", type="integer", nullable=true)
     */
    private $referenceVersion;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $configuration;

    /**
     * @var string
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $validation;

    /**
     * @var string
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $labels;

    /**
     * @var string
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $options;

    /**
     * @var string
     * @ORM\Column(name="content_channels", type="json_array", nullable=true)
     */
    private $contentChannels;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @return string
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
        $this->id = (int) $id;

        return $this;
    }

    /**
     * @return Elementtype
     */
    public function getElementtype()
    {
        return $this->elementtype;
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return $this
     */
    public function setElementtype($elementtype)
    {
        $this->elementtype = $elementtype;

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
     * @return int
     */
    public function getElementtypeStructure()
    {
        return $this->elementtypeStructure;
    }

    /**
     * @param ElementtypeStructure $elementtypeStructure
     *
     * @return $this
     */
    public function setElementtypeStructure(ElementtypeStructure $elementtypeStructure = null)
    {
        $this->elementtypeStructure = $elementtypeStructure;

        return $this;
    }

    /**
     * @return ElementtypeStructureNode
     */
    public function getParentNode()
    {
        return $this->parentNode;
    }

    /**
     * @param ElementtypeStructureNode $parentNode
     *
     * @return $this
     */
    public function setParentNode($parentNode)
    {
        $this->parentNode = $parentNode;

        return $this;
    }

    /**
     * @return string
     */
    public function getParentId()
    {
        if (!$this->parentNode) {
            return null;
        }

        return $this->getParentNode()->getId();
    }

    /**
     * @return string
     */
    public function getDsId()
    {
        return $this->dsId;
    }

    /**
     * @param string $dsId
     *
     * @return $this
     */
    public function setDsId($dsId)
    {
        $this->dsId = (string) $dsId;

        return $this;
    }

    /**
     * @return string
     */
    public function getParentDsId()
    {
        return $this->parentDsId;
    }

    /**
     * @param string $parentDsId
     *
     * @return $this
     */
    public function setParentDsId($parentDsId)
    {
        $this->parentDsId = $parentDsId ? (string) $parentDsId : null;

        return $this;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     *
     * @return $this
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

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
        $this->name = (string) $name;

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
        $this->comment = (string) $comment;

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
        $this->type = (string) $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getReferenceElementtype()
    {
        return $this->referenceElementtype;
    }

    /**
     * @param Elementtype $referenceElementtype
     *
     * @return $this
     */
    public function setReferenceElementtype(Elementtype $referenceElementtype = null)
    {
        $this->referenceElementtype = $referenceElementtype;

        return $this;
    }

    /**
     * @return int
     */
    public function getReferenceVersion()
    {
        return $this->referenceVersion;
    }

    /**
     * @param int $referenceVersion
     *
     * @return $this
     */
    public function setReferenceVersion($referenceVersion)
    {
        $this->referenceVersion = (int) $referenceVersion;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param array|null $configuration
     *
     * @return $this
     */
    public function setConfiguration(array $configuration = null)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @param string $key
     * @param string $default
     *
     * @return mixed
     */
    public function getConfigurationValue($key, $default = null)
    {
        if (is_array($this->configuration) && array_key_exists($key, $this->configuration)) {
            return $this->configuration[$key];
        }

        return $default;
    }

    /**
     * @return array|null
     */
    public function getValidation()
    {
        if (!is_array($this->validation)) {
            return null;
        }

        return $this->validation;
    }

    /**
     * @param array|null $validation
     *
     * @return $this
     */
    public function setValidation(array $validation = null)
    {
        $this->validation = $validation;

        return $this;
    }

    /**
     * @param string $key
     * @param string $default
     *
     * @return mixed
     */
    public function getValidationValue($key, $default = null)
    {
        if (is_array($this->validation) && array_key_exists($key, $this->validation)) {
            return $this->validation[$key];
        }

        return $default;
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param array|string $labels
     *
     * @return $this
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @param string $key
     * @param string $language
     *
     * @return string
     */
    public function getLabel($key, $language)
    {
        if (!is_array($this->labels) || !array_key_exists($key, $this->labels)) {
            return 'n/a';
        }

        if (!is_array($this->labels[$key]) || !array_key_exists($language, $this->labels[$key])) {
            return 'n/a';
        }

        return $this->labels[$key][$language];
    }

    /**
     * @return array|null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array|null $options
     *
     * @return $this
     */
    public function setOptions(array $options = null)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param string $key
     * @param string $default
     *
     * @return mixed
     */
    public function getOptionsValue($key, $default = null)
    {
        if (is_array($this->options) && array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }

        return $default;
    }

    /**
     * @return array|null
     */
    public function getContentChannels()
    {
        return $this->contentChannels;
    }

    /**
     * @param array|null $contentChannels
     *
     * @return $this
     */
    public function setContentChannels(array $contentChannels = null)
    {
        $this->contentChannels = $contentChannels;

        return $this;
    }

    /**
     * @param string $key
     * @param string $default
     *
     * @return mixed
     */
    public function getContentChannelsValue($key, $default = null)
    {
        if (is_array($this->contentChannels) && array_key_exists($key, $this->contentChannels)) {
            return $this->contentChannels[$key];
        }

        return $default;
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return $this->parentId === null;
    }

    /**
     * Is this field repeatable?
     *
     * @return bool
     */
    public function isRepeatable()
    {
        $max = (int) $this->getConfigurationValue('repeat_max');

        return $max > 1;
    }

    /**
     * Is this field repeatable?
     *
     * @return bool
     */
    public function isOptional()
    {
        $min = (int) $this->getConfigurationValue('repeat_min');
        $max = (int) $this->getConfigurationValue('repeat_max');

        return $min === 0 && $max > 0;
    }

    /**
     * @return array
     */
    public function getRepeatableDsIdPath()
    {
        $path = array();

        $node = $this;
        do {
            if ($node->isRepeatable() || $node->isOptional()) {
                $path[] = $node->getDsId();
            }
        } while ($node = $node->getParentNode());

        return $path;
    }

    /**
     * Is this field a reference?
     *
     * @return bool
     */
    public function isReference()
    {
        return self::FIELD_TYPE_REFERENCE === $this->getType();
    }

    /**
     * Is this field a reference root?
     *
     * @return bool
     */
    public function isReferenceRoot()
    {
        return self::FIELD_TYPE_REFERENCE_ROOT === $this->getType();
    }

    /**
     * @var bool
     */
    private $referenced = false;

    /**
     * @param bool $referenced
     *
     * @return $this
     */
    public function setReferenced($referenced = true)
    {
        $this->referenced = $referenced;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReferenced()
    {
        return $this->referenced;
    }
}
