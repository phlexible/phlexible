<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\GarbageCollector;

/**
 * Values collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ValuesCollection
{
    /**
     * @var array
     */
    private $activeValues = [];

    /**
     * @var array
     */
    private $inactiveValues = [];

    /**
     * @var array
     */
    private $removeValues = [];

    /**
     * ValuesCollection constructor.
     *
     * @param array $activeValues
     * @param array $inactiveValues
     * @param array $removeValues
     */
    public function __construct($activeValues = [], $inactiveValues = [], $removeValues = [])
    {
        $this->addActiveValues($activeValues);
        $this->addInactiveValues($inactiveValues);
        $this->addRemoveValues($removeValues);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function addActiveValue($value)
    {
        $value = trim($value);

        if ($value && !in_array($value, $this->activeValues)) {
            $this->activeValues[] = $value;
        }

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function addActiveValues($values)
    {
        foreach ($values as $value) {
            $this->addActiveValue($value);
        }

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function setActiveValues($values)
    {
        $this->activeValues = [];

        $this->addActiveValues($values);

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function removeActiveValue($value)
    {
        if (in_array($value, $this->activeValues)) {
            unset($this->activeValues[array_search($value, $this->activeValues)]);
        }

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function removeActiveValues($values)
    {
        foreach ($values as $value) {
            $this->removeActiveValue($value);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getActiveValues()
    {
        return $this->activeValues;
    }

    /**
     * @return int
     */
    public function countActiveValues()
    {
        return count($this->activeValues);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function addInactiveValue($value)
    {
        $value = trim($value);

        if ($value && !in_array($value, $this->inactiveValues)) {
            $this->inactiveValues[] = $value;
        }

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function addInactiveValues($values)
    {
        foreach ($values as $value) {
            $this->addInactiveValue($value);
        }

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function setInactiveValues($values)
    {
        $this->inactiveValues = [];

        $this->addInactiveValues($values);

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function removeInactiveValue($value)
    {
        if (in_array($value, $this->inactiveValues)) {
            unset($this->inactiveValues[array_search($value, $this->inactiveValues)]);
        }

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function removeInactiveValues($values)
    {
        foreach ($values as $value) {
            $this->removeInactiveValue($value);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getInactiveValues()
    {
        return $this->inactiveValues;
    }

    /**
     * @return int
     */
    public function countInactiveValues()
    {
        return count($this->inactiveValues);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function addRemoveValue($value)
    {
        $value = trim($value);

        if ($value && !in_array($value, $this->removeValues)) {
            $this->removeValues[] = $value;
        }

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function addRemoveValues($values)
    {
        foreach ($values as $value) {
            $this->addRemoveValue($value);
        }

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function setRemoveValues($values)
    {
        $this->removeValues = [];

        $this->addRemoveValues($values);

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function removeRemoveValue($value)
    {
        if (in_array($value, $this->removeValues)) {
            unset($this->removeValues[array_search($value, $this->removeValues)]);
        }

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function removeRemoveValues($values)
    {
        foreach ($values as $value) {
            $this->removeRemoveValue($value);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRemoveValues()
    {
        return $this->removeValues;
    }

    /**
     * @return int
     */
    public function countRemoveValues()
    {
        return count($this->removeValues);
    }

    /**
     * @param ValuesCollection $values
     *
     * @return $this
     */
    public function merge(ValuesCollection $values)
    {
        $this->addActiveValues($values->getActiveValues());
        $this->addInactiveValues($values->getInactiveValues());
        $this->addRemoveValues($values->getRemoveValues());

        return $this;
    }
}
