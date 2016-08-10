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
     * ValuesCollection constructor.
     *
     * @param array $activeValues
     * @param array $inactiveValues
     */
    public function __construct($activeValues = [], $inactiveValues = [])
    {
        $this->addActiveValues($activeValues);
        $this->addInactiveValues($inactiveValues);
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
     * @param ValuesCollection $values
     *
     * @return $this
     */
    public function merge(ValuesCollection $values)
    {
        foreach ($values->getActiveValues() as $value) {
            $this->addActiveValue($value);
        }

        foreach ($values->getInactiveValues() as $value) {
            $this->addInactiveValue($value);
        }

        return $this;
    }
}
