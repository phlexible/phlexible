<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\TreeBundle\Event\NodeEvent;
use Phlexible\Bundle\TreeBundle\Event\PublishNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\SetNodeOfflineEvent;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Tree node listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeNodeListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            TreeEvents::PUBLISH_NODE => 'onPublishNode',
            TreeEvents::SET_NODE_OFFLINE => 'onSetNodeOffline',
            TreeEvents::DELETE_NODE => 'onDeleteNode',
        );
    }

    /**
     * @param PublishNodeEvent $event
     */
    public function onPublishNode(PublishNodeEvent $event)
    {
        // TODO: repair
        return;

        $node = $event->getNode();
        $language = $event->getLanguage();

        self::_doTask($node, $language, 'Makeweb_Elements_Task_Publish');

        self::_queueDataSourceCleanup();
    }

    /**
     * @param SetNodeOfflineEvent $event
     */
    public function onSetNodeOffline(SetNodeOfflineEvent $event)
    {
        // TODO: repair
        return;

        $node = $event->getNode();
        $language = $event->getLanguage();

        self::_doTask($node, $language, 'Makeweb_Elements_Task_SetOffline');

        self::_queueDataSourceCleanup();
    }

    /**
     * @param NodeEvent $event
     */
    public function onDeleteNode(NodeEvent $event)
    {
        // TODO: repair
        return;

        $node = $event->getNode();
        $language = null;

        self::_doTask($node, $language, 'Makeweb_Elements_Task_Delete');

        self::_removeMatchingTask($node);
    }
}
