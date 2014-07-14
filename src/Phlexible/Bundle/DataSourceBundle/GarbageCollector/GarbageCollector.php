<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\GarbageCollector;

use Phlexible\Bundle\DataSourceBundle\DataSourceEvents;
use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Bundle\DataSourceBundle\Event\CollectionEvent;
use Phlexible\Bundle\DataSourceBundle\Model\DataSourceManagerInterface;
use Phlexible\Bundle\DataSourceBundle\Value\ValueCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Garbage collector for datas ource values.
 * - unused values can be removed
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class GarbageCollector
{
    /**
     * @var DataSourceManagerInterface
     */
    private $dataSourceManager;

    /**
     * Event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param DataSourceManagerInterface $dataSourceManager
     * @param EventDispatcherInterface   $dispatcher
     */
    public function __construct(DataSourceManagerInterface $dataSourceManager, EventDispatcherInterface $dispatcher)
    {
        $this->dataSourceManager = $dataSourceManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Start garbage collection.
     *
     * @param boolean $pretend
     *
     * @return array
     */
    public function run($pretend = false)
    {
        $numRemoved = 0;
        $numActivated = 0;
        $numDeactivated = 0;
        $offset = 0;

        foreach ($this->dataSourceManager->findBy(array(), null, 10, $offset) as $dataSource) {
            foreach ($dataSource->getValueBags() as $values) {
                $unused = $this->removeUnusedValues($values, $pretend);
                $numRemoved += count($unused);

                $activated = $this->activateActiveValues($values, $pretend);
                $numActivated += count($activated);

                $deactivated = $this->deactivateInactiveValues($values, $pretend);
                $numDeactivated += count($deactivated);
            }

            if (!$pretend) {
                $this->dataSourceManager->updateDataSource($dataSource);
            }
        }

        return array(
            'removed'     => $numRemoved,
            'activated'   => $numActivated,
            'deactivated' => $numDeactivated,
        );
    }

    /**
     * Remove unused values.
     *
     * @param DataSourceValueBag $valueBag
     * @param boolean            $pretend
     *
     * @return ValueCollection
     */
    private function removeUnusedValues(DataSourceValueBag $valueBag, $pretend = false)
    {
        $values = new ValueCollection($valueBag->getValues());
        $event = new CollectionEvent($valueBag, $values);
        if ($this->dispatcher->dispatch(DataSourceEvents::BEFORE_DELETE_VALUES, $event)->isPropagationStopped()) {
            return 0;
        }

        if ($pretend) {
            return $values;
        }

        if (count($values)) {
            // apply changes if there is changeable data
            foreach ($values as $value) {
                $valueBag->removeActiveValue($value);
                $valueBag->removeInactiveValue($value);
            }
        }

        $event = new CollectionEvent($valueBag, $values);
        $this->dispatcher->dispatch(DataSourceEvents::DELETE_VALUES, $event);

        return $values;
    }

    /**
     * Active active values.
     *
     * @param DataSourceValueBag $valueBag
     * @param boolean            $pretend
     *
     * @return ValueCollection
     */
    protected function activateActiveValues(DataSourceValueBag $valueBag, $pretend = false)
    {
        // dispatch pre event
        $values = new ValueCollection();
        $event = new CollectionEvent($valueBag, $values);
        if ($this->dispatcher->dispatch(DataSourceEvents::BEFORE_MARK_ACTIVE, $event)->isPropagationStopped()) {
            return 0;
        }

        if ($pretend) {
            return $values;
        }

        // get deactivatable values
        $intersectedValues = array_intersect($valueBag->getInactiveValues(), $values->toArray());

        $count = count($intersectedValues);
        if ($count) {
            // apply changes if there is changeable data
            foreach ($intersectedValues as $value) {
                $valueBag->addActiveValue($value);
                $valueBag->removeInactiveValue($value);
            }
        }

        // dispatch post event
        $event = new CollectionEvent($valueBag, $values);
        $this->dispatcher->dispatch(DataSourceEvents::MARK_ACTIVE, $event);

        return $values;
    }

    /**
     * Deactivate inactive values.
     *
     * @param DataSourceValueBag $valueBag
     * @param boolean            $pretend
     *
     * @return ValueCollection
     */
    protected function deactivateInactiveValues(DataSourceValueBag $valueBag, $pretend = false)
    {
        // dispatch pre event
        $values = new ValueCollection($valueBag->getActiveValues());
        $beforeEvent = new CollectionEvent($valueBag, $values);
        if ($this->dispatcher->dispatch(DataSourceEvents::BEFORE_MARK_INACTIVE, $beforeEvent)->isPropagationStopped()) {
            return 0;
        }

        if ($pretend) {
            return $values;
        }

        if (count($values)) {
            // apply changes if there is changeable data
            foreach ($values as $value) {
                $valueBag->addInactiveValue($value);
                $valueBag->removeActiveValue($value);
            }
        }

        // dispatch post event
        $event = new CollectionEvent($valueBag, $values);
        $this->dispatcher->dispatch(DataSourceEvents::MARK_INACTIVE, $event);

        return $values;
    }
}
