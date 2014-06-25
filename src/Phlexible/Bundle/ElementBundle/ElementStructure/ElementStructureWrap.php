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
class ElementStructureWrap implements \ArrayAccess
{
    /**
     * @var ElementStructure
     */
    private $structure;

    /**
     * @param ElementStructure $structure
     */
    public function __construct(ElementStructure $structure)
    {
        $this->structure = $structure;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->structure->getName();
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getValue($key)
    {
        if (!$this->structure->hasValue($key)) {
            return null;
        }

        return $this->structure->getValue($key);
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->structure->getValues();
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function find($name)
    {
        return $this->doFind($this->structure, $name);
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function first($name)
    {
        return $this->find($name);
    }

    public function doFind(ElementStructure $structure, $name)
    {
        if ($structure->hasValue($name)) {
            return $structure->getValue($name);
        }

        foreach ($structure->getStructures() as $childStructure) {
            if ($result = $this->doFind($childStructure, $name)) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return ElementStructure
     */
    public function children($name = null)
    {
        if (!$name) {
            $result = array();
            foreach ($this->structure->getStructures() as $structure) {
                $result[] = new self($structure);
            }

            return $result;
        }

        return $this->doChildren($this->structure, $name);
    }

    private function doChildren(ElementStructure $structure, $name)
    {
        $result = array();
        foreach ($structure->getStructures() as $childStructure) {
            if ($childStructure->getParentName() === $name) {
                $result[] = new self($childStructure);
            } else {
                $localResult = $this->doChildren($childStructure, $name);
                if ($localResult) {
                    $result = array_merge($result, $localResult);
                }
            }
        }

        if (count($result)) {
            return $result;
        }

        return null;
    }
    /**
     * @param mixed $offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
}}

