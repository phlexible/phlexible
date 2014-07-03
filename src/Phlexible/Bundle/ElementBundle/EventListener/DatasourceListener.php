<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\DataSourceBundle\DataSourceEvents;
use Phlexible\Bundle\DataSourceBundle\Event\CollectionEvent;
use Phlexible\Bundle\ElementBundle\Util\SuggestFieldUtil;
use Phlexible\Bundle\ElementBundle\Util\SuggestMetaFieldUtil;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Datasource listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DatasourceListener implements EventSubscriberInterface
{
    /**
     * @var SuggestFieldUtil
     */
    private $suggestFieldUtil;

    /**
     * @var SuggestMetaFieldUtil
     */
    private $suggestMetaFieldUtil;

    /**
     * @param SuggestFieldUtil     $suggestFieldUtil
     * @param SuggestMetaFieldUtil $suggestMetaFieldUtil
     */
    public function __construct(SuggestFieldUtil $suggestFieldUtil, SuggestMetaFieldUtil $suggestMetaFieldUtil)
    {
        $this->suggestFieldUtil = $suggestFieldUtil;
        $this->suggestMetaFieldUtil = $suggestMetaFieldUtil;
    }

    private function _queueDataSourceCleanup()
    {
        // add cleanup job for suggets fields
        MWF_Registry::getContainer()->queueService->addUniqueJob(
            new \Phlexible\Bundle\DataSourceBundle\Job\CleanupJob(),
            \Phlexible\Bundle\QueueBundle\QueueItem::PRIORITY_LOW
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            DataSourceEvents::BEFORE_MARK_ACTIVE   => 'onBeforeMarkActive',
            DataSourceEvents::BEFORE_MARK_INACTIVE => 'onBeforeMarkInactive',
            DataSourceEvents::BEFORE_DELETE_VALUES => 'onBeforeDeleteValues',
        );
    }

    /**
     * @param CollectionEvent $event
     */
    public function onBeforeDeleteValues(CollectionEvent $event)
    {
        // get id of data source to process
        $dataSource = $event->getDataSource();
        $dataSourceId = $dataSource->getId();
        $language = $dataSource->getLanguage();

        // fetch all data source values used in any element version
        $usedValues = $this->suggestFieldUtil->fetchUsedValues($dataSourceId, $language);

        // remove used values from collection
        $event->getCollection()->removeValuesByKey($usedValues);

        // fetch all data source values used in any element version
        $usedMetaValues = $this->suggestMetaFieldUtil->fetchUsedValues($dataSourceId, $language);

        // remove used values from collection
        $event->getCollection()->removeValuesByKey($usedMetaValues);
    }

    /**
     * @param CollectionEvent $event
     */
    public function onBeforeMarkInactive(CollectionEvent $event)
    {
        // get id of data source to process
        $dataSource = $event->getDataSource();
        $dataSourceId = $dataSource->getId();
        $language = $dataSource->getLanguage();

        // fetch all data source values used in element online versions
        $onlineValues = $this->suggestFieldUtil->fetchOnlineValues($dataSourceId, $language);

        // remove online values from collection
        $event->getCollection()->removeValuesByKey($onlineValues);

        // fetch all data source values used in element online versions
        $onlineMetaValues = $this->suggestMetaFieldUtil->fetchOnlineValues($dataSourceId, $language);

        // remove online values from collection
        $event->getCollection()->removeValuesByKey($onlineMetaValues);
    }

    /**
     * @param CollectionEvent $event
     */
    public function onBeforeMarkActive(CollectionEvent $event)
    {
        // get id of data source to process
        $dataSource = $event->getDataSource();
        $dataSourceId = $dataSource->getId();
        $language = $dataSource->getLanguage();

        // fetch all data source values used in element online versions
        $onlineValues = $this->suggestFieldUtil->fetchOnlineValues($dataSourceId, $language);

        // remove offline values from collection
        $event->getCollection()->addValues($onlineValues);

        // fetch all data source values used in element online versions
        $onlineMetaValues = $this->suggestMetaFieldUtil->fetchOnlineValues($dataSourceId, $language);

        // remove offline values from collection
        $event->getCollection()->addValues($onlineMetaValues);
    }
}
