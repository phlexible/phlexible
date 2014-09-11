<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Model;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;

/**
 * Element structure
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementStructure implements \IteratorAggregate
{
    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * @var ElementVersion
     */
    private $elementVersion;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $repeatableId;

    /**
     * @var string
     */
    private $dsId;

    /**
     * @var string
     */
    private $repeatableDsId;

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
     * @var ElementStructureValue[]
     */
    private $values = array();

    /**
     * Clone
     */
    public function __clone()
    {
        $this->elementVersion = null;
    }

    /**
     * @return string
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * @param string $defaultLanguage
     *
     * @return $this
     */
    public function setDefaultLanguage($defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;

        return $this;
    }

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
     * @return int
     */
    public function getRepeatableId()
    {
        return $this->repeatableId;
    }

    /**
     * @param int $parentId
     *
     * @return $this
     */
    public function setRepeatableId($parentId)
    {
        $this->repeatableId = $parentId !== null ? (int) $parentId : null;

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
    public function getRepeatableDsId()
    {
        return $this->repeatableDsId;
    }

    /**
     * @param string $parentDsId
     *
     * @return $this
     */
    public function setRepeatableDsId($parentDsId)
    {
        $this->repeatableDsId = $parentDsId;

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
        $this->parentName = $parentName;

        return $this;
    }

    /**
     * @param ElementStructure $elementStructure
     *
     * @return $this
     */
    public function addStructure(ElementStructure $elementStructure)
    {
        $elementStructure
            ->setRepeatableId($this->getId())
            ->setRepeatableDsId($this->getDsId());

        $this->structures[] = $elementStructure;

        return $this;
    }

    /**
     * @param int $id
     *
     * @return ElementStructure|null
     */
    public function findStructure($id)
    {
        if ($this->getId() === $id) {
            return $this;
        }

        foreach ($this->structures as $childStructure) {
            $result = $childStructure->findStructure($id);
            if ($result) {
                return $result;
            }
        }

        return null;
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
        $this->values[$value->getLanguage()][$value->getName()] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @param string $language
     *
     * @return ElementStructureValue
     */
    public function getValue($name, $language = null)
    {
        if (!$this->hasValue($name, $language)) {
            return null;
        }

        if (!$language) {
            $language = $this->defaultLanguage;
        }

        return $this->values[$language][$name];
    }

    /**
     * @param string $name
     * @param string $language
     *
     * @return bool
     */
    public function hasValue($name, $language = null)
    {
        if (!$language) {
            $language = $this->defaultLanguage;
        }

        return isset($this->values[$language][$name]);
    }

    /**
     * @param string $language
     *
     * @return ElementStructureValue[]
     */
    public function getValues($language = null)
    {
        if (!$language) {
            $language = $this->defaultLanguage;
        }

        if (!isset($this->values[$language])) {
            return array();
        }

        return $this->values[$language];
    }

    /**
     * @param string $dsId
     * @param string $language
     *
     * @return bool
     */
    public function hasValueByDsId($dsId, $language = null)
    {
        if (!$language) {
            $language = $this->defaultLanguage;
        }

        foreach ($this->values[$language] as $value) {
            if ($value->getDsId() === $dsId) {
                return true;
            }
        }

        return false;

    }

    /**
     * @param string $dsId
     * @param string $language
     *
     * @return ElementStructureValue|null
     */
    public function getValueByDsId($dsId, $language = null)
    {
        if (!$language) {
            $language = $this->defaultLanguage;
        }

        if (!isset($this->values[$language])) {
            return null;
        }

        foreach ($this->values[$language] as $value) {
            if ($value->getDsId() === $dsId) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param string $language
     */
    public function removeLanguage($language)
    {
        unset($this->values[$language]);
        foreach ($this->structures as $structure) {
            $structure->removeLanguage($language);
        }
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return array_keys($this->values);
    }

    /**
     * @return ElementStructureIterator
     */
    public function getIterator()
    {
        return new ElementStructureIterator($this->getStructures());
    }

    /**
     * @param string $name
     *
     * @return ElementStructure|ElementStructureValue|null
     */
    public function find($name)
    {
        if ($this->hasValue($name)) {
            return $this->getValue($name);
        }

        foreach ($this->getStructures() as $childStructure) {
            if ($result = $childStructure->find($name)) {
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
            return $this->getStructures();
        }

        $result = array();
        foreach ($this->getStructures() as $childStructure) {
            if ($childStructure->getParentName() === $name) {
                $result[] = $childStructure;
            } else {
                $localResult = $childStructure->children($name);
                if ($localResult) {
                    $result = array_merge($result, $localResult);
                }
            }
        }

        if (!count($result)) {
            return null;
        }

        return $result;
    }

    /**
     * @param bool $withValues
     * @param int  $depth
     *
     * @return string
     */
    public function dump($withValues = true, $depth = 0)
    {
        //$dump = str_repeat(' ', $depth * 2) . '+ '.$this->getName().' ('.$this->getParentName().') '.$this->getId().' '.$this->getDsId().PHP_EOL;
        $dump = str_repeat(' ', $depth * 2) . '+ <fg=green>'.$this->getName().'</fg=green> ('.$this->getParentName().') '.$this->getId().PHP_EOL;
        if ($withValues) {
            foreach ($this->values as $values) {
                foreach ($values as $value) {
                    //$dump .= str_repeat(' ', $depth * 2 + 2).'= '.$value->getName().' '.$value->getDsId().' '.(is_array($value->getValue()) ? json_encode($value->getValue()) : $value->getValue()).PHP_EOL;
                    $dump .= str_repeat(' ', $depth * 2 + 2).'= <fg=yellow>'.$value->getName().'</fg=yellow>: ['.$value->getLanguage().','.$value->getId().'] <fg=cyan>'.(is_array($value->getValue()) ? json_encode($value->getValue()) : $value->getValue()).'</fg=cyan>'.PHP_EOL;
                }
            }
        }
        foreach ($this->structures as $structure) {
            $dump .= $structure->dump($withValues, $depth + 1);
        }
        return $dump;
    }
}

