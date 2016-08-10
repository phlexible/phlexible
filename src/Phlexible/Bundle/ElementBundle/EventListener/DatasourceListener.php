<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\DataSourceBundle\DataSourceEvents;
use Phlexible\Bundle\DataSourceBundle\Event\GarbageCollectEvent;
use Phlexible\Bundle\ElementBundle\ElementEvents;
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

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            DataSourceEvents::BEFORE_GARBAGE_COLLECT => 'onBeforeGarbageCollect',
        ];
    }

    /**
     * @param GarbageCollectEvent $event
     */
    public function onBeforeGarbageCollect(GarbageCollectEvent $event)
    {
        $values = $event->getDataSourceValueBag();
        $collectedValues = $event->getCollectedValues();

        //$datasource = $values->getDatasource();
        //$datasourceId = $datasource->getId();
        //$language = $values->getLanguage();

        // fetch all data source values used in element online versions
        $collectedValues->merge($this->suggestFieldUtil->fetchUsedValues($values));

        // fetch all meta data source values used in element online versions
        $collectedValues->merge($this->suggestMetaFieldUtil->fetchUsedValues($values));
    }
}
