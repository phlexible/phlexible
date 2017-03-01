<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\Model;

/**
 * Meta data.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaData implements MetaDataInterface, \Countable
{
    /**
     * @var MetaSet
     */
    private $metaSet;

    /**
     * @var array
     */
    private $values = [];

    /**
     * @param MetaSetInterface $metaSet
     */
    public function __construct(MetaSetInterface $metaSet)
    {
        $this->metaSet = $metaSet;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaSet()
    {
        return $this->metaSet;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaSet(MetaSetInterface $metaSet)
    {
        $this->metaSet = $metaSet;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguages()
    {
        return array_keys($this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function get($field, $language = null)
    {
        if (!isset($this->values[$language][$field])) {
            return null;
        }

        return $this->values[$language][$field];
    }

    /**
     * {@inheritdoc}
     */
    public function set($field, $value, $language = null)
    {
        $this->values[$language][$field] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($field, $language = null)
    {
        return isset($this->values[$language][$field]);
    }

    /**
     * {@inheritdoc}
     */
    public function __get($field)
    {
        return $this->get($field);
    }

    /**
     * {@inheritdoc}
     */
    public function __set($field, $value)
    {
        return $this->set($field, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function __isset($field)
    {
        return $this->has($field);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return bool
     */
    public function hasValues()
    {
        return $this->count() > 0;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->values);
    }
}
