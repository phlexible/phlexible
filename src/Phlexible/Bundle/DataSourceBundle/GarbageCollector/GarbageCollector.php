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
use Phlexible\Bundle\DataSourceBundle\Event\GarbageCollectEvent;
use Phlexible\Bundle\DataSourceBundle\Model\DataSourceManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Garbage collector for datas ource values.
 * - unused values can be removed
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class GarbageCollector
{
    const MODE_REMOVE_UNUSED = 'remove_unused';
    const MODE_REMOVE_UNUSED_AND_INACTIVE = 'remove_unused_inactive';
    const MODE_MARK_UNUSED_INACTIVE = 'inactive';

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
     * @param string  $mode
     * @param boolean $pretend
     *
     * @return array
     */
    public function run($mode = self::MODE_MARK_UNUSED_INACTIVE, $pretend = false)
    {
        $nums = array();

        $limit = 10;
        $offset = 0;
        foreach ($this->dataSourceManager->findBy(array(), null, $limit, $offset) as $dataSource) {
            foreach ($dataSource->getValueBags() as $values) {
                $num = $this->garbageCollect($values, $mode, $pretend);

                $nums[$dataSource->getTitle()][$values->getLanguage()] = $num;
            }

            if (!$pretend) {
                $this->dataSourceManager->updateDataSource($dataSource);
            }

            $offset += $limit;
        }

        return $nums;
    }

    /**
     * @param DataSourceValueBag $valueBag
     * @param string             $mode
     * @param bool               $pretend
     *
     * @return array
     */
    private function garbageCollect(DataSourceValueBag $valueBag, $mode, $pretend = false)
    {
        $event = new GarbageCollectEvent($valueBag);
        if ($this->dispatcher->dispatch(DataSourceEvents::BEFORE_GARBAGE_COLLECT, $event)->isPropagationStopped()) {
            return array();
        }

        $activeValues = $event->getActiveValues();
        $inactiveValues = $event->getInactiveValues();

        #ld('raw active', $activeValues);ld('raw inactive', $inactiveValues);

        $values = $valueBag->getValues();
        $removeValues = array_diff($values, $activeValues, $inactiveValues);
        $inactiveValues = array_diff($inactiveValues, $activeValues);

        #ld('remove', $removeValues);ld('active', $activeValues);ld('inactive', $inactiveValues);exit;

        // for MODE_REMOVE_UNUSED is no change necessary
        if ($mode === self::MODE_MARK_UNUSED_INACTIVE) {
            $inactiveValues = array_merge($inactiveValues, $removeValues);
            sort($inactiveValues);
            $removeValues = array();
        } elseif ($mode === self::MODE_REMOVE_UNUSED_AND_INACTIVE) {
            $removeValues = array_merge($removeValues, $inactiveValues);
            sort($removeValues);
            $inactiveValues = array();
        }

        if (!$pretend) {
            if (count($removeValues)) {
                $removeValues = array_values(array_unique($removeValues));
                // apply changes if there is changeable data
                foreach ($removeValues as $value) {
                    $valueBag->removeActiveValue($value);
                    $valueBag->removeInactiveValue($value);
                }
            }

            if (count($activeValues)) {
                $activeValues = array_values(array_unique($activeValues));
                // apply changes if there is changeable data
                foreach ($activeValues as $value) {
                    $valueBag->addActiveValue($value);
                    $valueBag->removeInactiveValue($value);
                }
            }

            if (count($inactiveValues)) {
                // apply changes if there is changeable data
                $inactiveValues = array_values(array_unique($inactiveValues));
                foreach ($inactiveValues as $value) {
                    $valueBag->addInactiveValue($value);
                    $valueBag->removeActiveValue($value);
                }
            }
        }

        $event = new GarbageCollectEvent($valueBag);
        $this->dispatcher->dispatch(DataSourceEvents::GARBAGE_COLLECT, $event);

        return array(
            'active'   => $activeValues,
            'inactive' => $inactiveValues,
            'remove'   => $removeValues,
        );
    }
}
