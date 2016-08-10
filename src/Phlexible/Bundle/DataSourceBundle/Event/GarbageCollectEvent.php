<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Event;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Bundle\DataSourceBundle\GarbageCollector\ValuesCollection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Garbage collect event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GarbageCollectEvent extends Event
{
    /**
     * @var DataSourceValueBag
     */
    private $values;

    /**
     * @var ValuesCollection
     */
    private $collectedValues;

    /**
     * @param DataSourceValueBag $values
     */
    public function __construct(DataSourceValueBag $values)
    {
        $this->values = $values;

        $this->collectedValues = new ValuesCollection();
    }

    /**
     * @return DataSourceValueBag
     */
    public function getDataSourceValueBag()
    {
        return $this->values;
    }

    /**
     * @return ValuesCollection
     */
    public function getCollectedValues()
    {
        return $this->collectedValues;
    }

    /**
     * @param string|array $values
     *
     * @return $this
     */
    public function markActive($values)
    {
        if (is_array($values)) {
            $this->collectedValues->addActiveValues($values);
        } else {
            $this->collectedValues->addActiveValue($values);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getActiveValues()
    {
        return $this->collectedValues->getActiveValues();
    }

    /**
     * @param string|array $values
     *
     * @return $this
     */
    public function markInactive($values)
    {
        if (is_array($values)) {
            $this->collectedValues->addInactiveValues($values);
        } else {
            $this->collectedValues->addInactiveValue($values);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getInactiveValues()
    {
        return $this->collectedValues->getInactiveValues();
    }
}
