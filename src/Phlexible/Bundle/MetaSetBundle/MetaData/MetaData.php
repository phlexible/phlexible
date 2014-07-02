<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\MetaData;

/**
 * Meta data
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaData implements MetaDataInterface, \Countable
{
    /**
     * @var array
     */
    private $identifiers;

    /**
     * @var array
     */
    private $values = array();

    /**
     * {@inheritdoc}
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentifiers(array $identifiers)
    {
        $this->identifiers = $identifiers;

        return $this;
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
     * @see get()
     */
    public function __get($field)
    {
        return $this->get($field);
    }

    /**
     * @see set()
     */
    public function __set($field, $value)
    {
        return $this->set($field, $value);
    }

    /**
     * @see has()
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
