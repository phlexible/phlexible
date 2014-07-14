<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Value;

use Traversable;

/**
 * Value collection
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class ValueCollection implements \Countable, \IteratorAggregate
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
     * @param array $values
     *
     * @return $this
     */
    public function removeValues(array $values)
    {
        $this->values = array_diff($this->values, $values);

        return $this;
    }

    /**
     * Hold values from collection by its key value.
     *
     * @param array $values
     *
     * @return $this
     */
    public function holdValues(array $values)
    {
        $this->values = array_intersect($this->values, $values);

        return $this;
    }

    /**
     * Get values in collection.
     *
     * @return array
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

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->values);
    }
}