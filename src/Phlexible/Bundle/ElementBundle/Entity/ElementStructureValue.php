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
 * Element structure data
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="element_structure_value")
 */
class ElementStructureValue
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="data_id", type="integer")
     */
    private $dataId;

    /**
     * @var Element
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Element")
     * @ORM\JoinColumn(name="eid", referencedColumnName="eid")
     */
    private $element;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    private $language;

    /**
     * @var string
     * @ORM\Column(name="ds_id", type="string", length=36, options={"fixed"=true})
     */
    private $dsId;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var int
     * @ORM\Column(name="repeatable_id", type="integer", length=255, nullable=true)
     */
    private $repeatableId;

    /**
     * @var string
     * @ORM\Column(name="repeatable_ds_id", type="string", length=36, nullable=true, options={"fixed"=true})
     */
    private $repeatableDsId;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @var string
     * @ORM\Column(type="json_array", length=255, nullable=true)
     */
    private $options;

    /**
     * @return int
     */
    public function getDataId()
    {
        return $this->dataId;
    }

    /**
     * @param int $dataId
     *
     * @return $this
     */
    public function setDataId($dataId)
    {
        $this->dataId = $dataId;

        return $this;
    }

    /**
     * @return \Phlexible\Bundle\ElementBundle\Entity\Element
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param \Phlexible\Bundle\ElementBundle\Entity\Element $element
     *
     * @return $this
     */
    public function setElement($element)
    {
        $this->element = $element;

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
        $this->dsId = $dsId;

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
     * @return int
     */
    public function getRepeatableId()
    {
        return $this->repeatableId;
    }

    /**
     * @param int $repeatableId
     *
     * @return $this
     */
    public function setRepeatableId($repeatableId)
    {
        $this->repeatableId = $repeatableId;

        return $this;
    }

    /**
     * @return string
     */
    public function getRepeatableDsId()
    {
        return $this->repeatableDsId;
    }

    /**
     * @param string $repeatableDsId
     *
     * @return $this
     */
    public function setRepeatableDsId($repeatableDsId)
    {
        $this->repeatableDsId = $repeatableDsId;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }
}