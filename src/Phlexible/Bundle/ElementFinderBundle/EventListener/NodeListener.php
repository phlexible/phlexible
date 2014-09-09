<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\EventListener;

use Phlexible\Bundle\ElementFinderBundle\ElementFinder\CatchHelper;
use Phlexible\Bundle\TreeBundle\Event\NodeEvent;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Node listener
 *
 * @author Marco Fischer <mf@brainbits.net>
 */
class NodeListener implements EventSubscriberInterface
{
    /**
     * @var CatchHelper
     */
    private $catchHelper;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            TreeEvents::PUBLISH_NODE         => 'onPublishNode',
            TreeEvents::SET_NODE_OFFLINE     => 'onSetNodeOffline',
            TreeEvents::DELETE_NODE          => 'onDeleteNode',
            TreeEvents::UPDATE_NODE          => 'onUpdateNode',
            TreeEvents::CREATE_NODE_INSTANCE => 'onCreateNodeInstance',
        );
    }

    /**
     * @param CatchHelper $catchHelper
     */
    public function __construct(CatchHelper $catchHelper)
    {
        $this->catchHelper = $catchHelper;
    }

    /**
     * @param NodeEvent $event
     */
    public function onPublishNode(NodeEvent $event)
    {
        $node = $event->getNode();
        $eid = $node->getTypeId();

        $this->catchHelper->updateOnline($eid);
    }

    /**
     * @param NodeEvent $event
     */
    public function onSetNodeOffline(NodeEvent $event)
    {
        $node = $event->getNode();
        $tid = $node->getId();
        $language = $event->getLanguage();

        $this->catchHelper->removeOnlineByTidAndLanguage($tid, $language);
    }

    /**
     * @param NodeEvent $event
     */
    public function onDeleteNode(NodeEvent $event)
    {
        $node = $event->getNode();
        $tid = $node->getId();

        $this->catchHelper->removeByTid($tid);
    }

    /**
     * @param NodeEvent $event
     */
    public function onUpdateNode(NodeEvent $event)
    {
        $node = $event->getNode();
        $eid = $node->getTypeId();

        $this->catchHelper->updatePreview($eid);
    }

    /**
     * @param NodeEvent $event
     */
    public function onCreateNodeInstance(NodeEvent $event)
    {
        $node = $event->getNode();
        $eid = $node->getTypeId();

        $this->catchHelper->updatePreview($eid);
    }
}
