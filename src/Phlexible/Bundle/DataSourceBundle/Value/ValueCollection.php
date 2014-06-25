<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle;

/**
 * Value collection
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class ValueCollection implements \Countable
{
    /**
     * Data source value collection.
     *
     * @var array <id> => <key>
     */
    private $values;

    /**
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        $this->values = $values;
    }

    /**
     * Set new value collection.
     *
     * @param array $values <id> => <key>
     *
     * @return $this
     */
    public function addValues(array $values)
    {
        $this->values = array_merge($values);

        return $this;
    }

    /**
     * Set new value collection.
     *
     * @param array $values <id> => <key>
     *
     * @return $this
     */
    public function setValues(array $values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Remove values from collection by its key value.
     *
     * @param array $keys
     *
     * @return $this
     */
    public function removeValuesByKey(array $keys)
    {
        $this->values = array_diff($this->values, $keys);

        return $this;
    }

    /**
     * Hold values from collection by its key value.
     *
     * @param array $keys
     *
     * @return $this
     */
    public function holdValuesByKey(array $keys)
    {
        $this->values = array_intersect($this->values, $keys);

        return $this;
    }

    /**
     * Remove values from collection by its key value.
     *
     * @param array $ids
     *
     * @return $this
     */
    public function removeValuesById(array $ids)
    {
        $this->values = array_diff_key($this->values, array_flip($ids));

        return $this;
    }

    /**
     * Get values in collection.
     *
     * @return array <id> => <key>
     */
    public function toArray()
    {
        return $this->values;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->values);
    }
}