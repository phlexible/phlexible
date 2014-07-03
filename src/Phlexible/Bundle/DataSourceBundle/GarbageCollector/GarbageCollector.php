<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\GarbageCollector;

use Phlexible\Bundle\DataSourceBundle\DataSourceEvents;
use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;
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
     * @param array|string $ids id/ids of data sources [Optional]
     *
     * @return array
     */
    public function run($ids = null)
    {
        if (null === $ids) {
            // if none id is given -> process all data sources
            $ids = $this->dataSourceManager->getAllDataSourceIds();
        } elseif (!is_array($ids)) {
            // if a single data source is selected
            $ids = array($ids);
        }

        // run garbage collector on all data sources
        return $this->doRun($ids);
    }

    /**
     * Start garbage collection.
     *
     * @param array $ids ids of data sources to process
     *
     * @return array
     */
    private function doRun(array $ids)
    {
        $removed = 0;
        $activated = 0;
        $deactivated = 0;

        foreach ($ids as $id) {
            $languages = $this->dataSourceManager->getAllDataSourceLanguages($id);
            $dataSource = $this->dataSourceManager->find($id);
            foreach ($languages as $language) {
                $removed += $this->removeUnusedValues($dataSource, $language);
                $activated += $this->activateActiveValues($dataSource, $language);
                $deactivated += $this->deactivateInactiveValues($dataSource, $language);
            }
        }

        return array(
            'removed'     => $removed,
            '$activated'  => $activated,
            'deactivated' => $deactivated,
        );
    }

    /**
     * Remove unused values.
     *
     * @param DataSource $dataSource
     *
     * @return int
     */
    private function removeUnusedValues(DataSource $dataSource)
    {
        // dispatch pre event
        $collection = new ValueCollection($dataSource->getKeys());
        $event = new CollectionEvent($dataSource, $collection);
        if ($this->dispatcher->dispatch(DataSourceEvents::BEFORE_DELETE_VALUES, $event)->isPropagationStopped()) {
            return 0;
        }

        $count = count($collection);

        if ($count) {
            // get deactivatable values
            $removeable = $collection->toArray();

            // apply changes if there is changeable data
            $dataSource->removeKeys($removeable);
            $this->dataSourceManager->updateDataSource($dataSource);
        }

        // dispatch post event
        $event = new CollectionEvent($dataSource, $collection);
        $this->dispatcher->dispatch(DataSourceEvents::DELETE_VALUES, $event);

        return $count;
    }

    /**
     * Active active values.
     *
     * @param DataSource $dataSource
     *
     * @return int
     */
    protected function activateActiveValues(DataSource $dataSource)
    {
        // dispatch pre event
        $collection = new ValueCollection();
        $event = new CollectionEvent($dataSource, $collection);
        if ($this->dispatcher->dispatch(DataSourceEvents::BEFORE_MARK_ACTIVE, $event)->isPropagationStopped()) {
            return 0;
        }

        // get deactivatable values
        $activatable = array_intersect($dataSource->getInactiveKeys(), $collection->toArray());

        $count = count($activatable);
        if ($count) {
            // apply changes if there is changeable data
            $dataSource->activateKeys($activatable);
            $this->dataSourceManager->updateDataSource($dataSource);
        }

        // dispatch post event
        $event = new CollectionEvent($dataSource, $collection);
        $this->dispatcher->dispatch(DataSourceEvents::MARK_ACTIVE, $event);

        return $count;
    }

    /**
     * Deactivate inactive values.
     *
     * @param DataSource $dataSource
     *
     * @return int
     */
    protected function deactivateInactiveValues(DataSource $dataSource)
    {
        // dispatch pre event
        $collection = new ValueCollection($dataSource->getActiveKeys());
        $beforeEvent = new CollectionEvent($dataSource, $collection);
        if ($this->dispatcher->dispatch(DataSourceEvents::BEFORE_MARK_INACTIVE, $beforeEvent)->isPropagationStopped()) {
            return 0;
        }

        // get deactivatable values
        $deactivatable = $collection->toArray();

        $count = count($deactivatable);
        if ($count) {
            // apply changes if there is changeable data
            $dataSource->deactivateKeys($deactivatable);
            $this->dataSourceManager->updateDataSource($dataSource);
        }

        // dispatch post event
        $event = new CollectionEvent($dataSource, $collection);
        $this->dispatcher->dispatch(DataSourceEvents::BEFORE_MARK_ACTIVE, $event);

        return $count;
    }
}
