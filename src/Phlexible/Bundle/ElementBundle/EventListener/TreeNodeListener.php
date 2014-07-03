<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

/**
 * Tree node listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeNodeListener
{
    public function onPublishNode(Makeweb_Elements_Event_PublishNode $event)
    {
        $node = $event->getNode();
        $language = $event->getLanguage();

        self::_doTask($node, $language, 'Makeweb_Elements_Task_Publish');

        self::_queueDataSourceCleanup();
    }

    public function onSetNodeOffline(Makeweb_Elements_Event_SetNodeOffline $event)
    {
        $node = $event->getNode();
        $language = $event->getLanguage();

        self::_doTask($node, $language, 'Makeweb_Elements_Task_SetOffline');

        self::_queueDataSourceCleanup();
    }

    public function onDeleteNode(Makeweb_Elements_Event_DeleteNode $event)
    {
        $node = $event->getNode();
        $language = null;

        self::_doTask($node, $language, 'Makeweb_Elements_Task_Delete');

        self::_removeMatchingTask($node);
    }
}
