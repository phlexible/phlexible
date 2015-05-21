<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\DataSource\Event;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
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
     * @var array
     */
    private $activeValues = [];

    /**
     * @var array
     */
    private $inactiveValues = [];

    /**
     * @param DataSourceValueBag $values
     */
    public function __construct(DataSourceValueBag $values)
    {
        $this->values = $values;
    }

    /**
     * @return DataSourceValueBag
     */
    public function getDataSourceValueBag()
    {
        return $this->values;
    }

    /**
     * @param string|array $values
     *
     * @return $this
     */
    public function markActive($values)
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            $this->activeValues[] = $value;
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
     * @param string|array $values
     *
     * @return $this
     */
    public function markInactive($values)
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            $this->inactiveValues[] = $value;
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
}
