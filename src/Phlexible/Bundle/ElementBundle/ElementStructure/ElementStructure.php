<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementStructure;

use Phlexible\Bundle\ElementBundle\ElementVersion\ElementVersion;

/**
 * Element structure
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementStructure implements \IteratorAggregate
{
    /**
     * @var ElementVersion
     */
    private $elementVersion;

    /**
     * @var integer
     */
    private $id;

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
    private $name;

    /**
     * @var string
     */
    private $parentName;

    /**
     * @var ElementStructure[]
     */
    private $structures = array();

    /**
     * @var ElementStructureValue
     */
    private $values = array();

    /**
     * @return ElementVersion
     */
    public function getElementVersion()
    {
        return $this->elementVersion;
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @return $this
     */
    public function setElementVersion(ElementVersion $elementVersion)
    {
        $this->elementVersion = $elementVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (integer) $id;

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
        $this->parentDsId = (string) $parentDsId;

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
    public function getParentName()
    {
        return $this->parentName;
    }

    /**
     * @param string $parentName
     *
     * @return $this
     */
    public function setParentName($parentName)
    {
        $this->parentName = (string) $parentName;

        return $this;
    }

    /**
     * @param ElementStructure $elementStructure
     *
     * @return $this
     */
    public function addStructure(ElementStructure $elementStructure)
    {
        $this->structures[] = $elementStructure;

        return $this;
    }

    /**
     * @return ElementStructure[]
     */
    public function getStructures()
    {
        return $this->structures;
    }

    /**
     * @param ElementStructureValue $value
     *
     * @return $this;
     */
    public function setValue(ElementStructureValue $value)
    {
        $this->values[$value->getName()] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return ElementStructureValue
     */
    public function getValue($name)
    {
        return $this->values[$name];
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function hasValue($name)
    {
        return isset($this->values[$name]);
    }

    /**
     * @return ElementStructureValue[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param string $dsId
     *
     * @return boolean
     */
    public function hasValueByDsId($dsId)
    {
        foreach ($this->values as $value) {
            if ($value->getDsId() === $dsId) {
                return true;
            }
        }

        return false;

    }

    /**
     * @param string $dsId
     *
     * @return ElementStructureValue|null
     */
    public function getValueByDsId($dsId)
    {
        foreach ($this->values as $value) {
            if ($value->getDsId() === $dsId) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @return ElementStructureIterator
     */
    public function getIterator()
    {
        return new ElementStructureIterator($this->getStructures());
    }
}

