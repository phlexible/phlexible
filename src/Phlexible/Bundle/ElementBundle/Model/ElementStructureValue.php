<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Model;

/**
 * Element structure value
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementStructureValue
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $dsId;

    /**
     * @var string
     */
    private $language;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $dataType;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $attributes = array();

    /**
     * @param int    $id
     * @param string $dsId
     * @param string $language
     * @param string $type
     * @param string $dataType
     * @param string $name
     * @param string $value
     * @param array  $options
     */
    public function __construct($id, $dsId, $language, $type, $dataType, $name, $value, array $options = null)
    {
        $this->id = (int) $id;
        $this->dsId = $dsId;
        $this->language = $language;
        $this->name = $name;
        $this->type = $type;
        $this->dataType = $dataType;
        $this->value = $value;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * @return int
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
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * @param string $dataType
     *
     * @return $this
     */
    public function setDataType($dataType)
    {
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * @param string     $key
     * @param mixed|null $defaultValue
     *
     * @return mixed|null
     */
    public function getAttribute($key, $defaultValue = null)
    {
        if ($this->hasAttribute($key)) {
            return $this->attributes[$key];
        }

        return $defaultValue;
    }
}

