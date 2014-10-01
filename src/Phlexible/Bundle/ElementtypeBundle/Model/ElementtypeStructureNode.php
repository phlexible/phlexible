<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Model;

/**
 * Elementtype structure node
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class ElementtypeStructureNode
{
    const FIELD_TYPE_REFERENCE = 'reference';
    const FIELD_TYPE_REFERENCE_ROOT = 'referenceroot';

    /**
     * @var ElementtypeStructureNode
     */
    private $parentNode;

    /**
     * @var string
     */
    private $dsId;

    /**
     * @var string
     */
    private $parentDsId;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $configuration;

    /**
     * @var string
     */
    private $validation;

    /**
     * @var string
     */
    private $labels;

    /**
     * @var string
     */
    private $contentChannels;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var int
     */
    private $referenceElementtypeId;

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
    public function setParentNode(ElementtypeStructureNode $parentNode = null)
    {
        if ($parentNode) {
            $this->parentDsId = $parentNode->getDsId();
        }

        $this->parentNode = $parentNode;

        return $this;
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
        return $this->parentDsId === null;
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

    /**
     * @return int
     */
    public function getReferenceElementtypeId()
    {
        return $this->referenceElementtypeId;
    }

    /**
     * @param int $referenceElementtypeId
     *
     * @return $this
     */
    public function setReferenceElementtypeId($referenceElementtypeId)
    {
        $this->referenceElementtypeId = $referenceElementtypeId;

        return $this;
    }
}
