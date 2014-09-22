<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\EventListener;

use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\Event\SaveNodeDataEvent;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\LookupBuilder;
use Phlexible\Bundle\TreeBundle\Event\NodeEvent;
use Phlexible\Bundle\TreeBundle\Event\SetNodeOfflineEvent;
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
     * @var LookupBuilder
     */
    private $lookupBuilder;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            TreeEvents::CREATE_NODE          => 'onCreateNode',
            TreeEvents::CREATE_NODE_INSTANCE => 'onCreateNodeInstance',
            ElementEvents::SAVE_NODE_DATA    => 'onSaveNodeData',
            TreeEvents::PUBLISH_NODE         => 'onPublishNode',
            TreeEvents::SET_NODE_OFFLINE     => 'onSetNodeOffline',
            TreeEvents::DELETE_NODE          => 'onDeleteNode',
        );
    }

    /**
     * @param LookupBuilder $lookupBuilder
     */
    public function __construct(LookupBuilder $lookupBuilder)
    {
        $this->lookupBuilder = $lookupBuilder;
    }

    /**
     * @param NodeEvent $event
     */
    public function onCreateNode(NodeEvent $event)
    {
        $node = $event->getNode();

        $this->lookupBuilder->updatePreview($node);
    }

    /**
     * @param NodeEvent $event
     */
    public function onCreateNodeInstance(NodeEvent $event)
    {
        $node = $event->getNode();

        $this->lookupBuilder->updatePreview($node);
    }

    /**
     * @param SaveNodeDataEvent $event
     */
    public function onSaveNodeData(SaveNodeDataEvent $event)
    {
        $node = $event->getNode();

        $this->lookupBuilder->updatePreview($node);
    }

    /**
     * @param NodeEvent $event
     */
    public function onPublishNode(NodeEvent $event)
    {
        $node = $event->getNode();

        $this->lookupBuilder->updateOnline($node);
    }

    /**
     * @param SetNodeOfflineEvent $event
     */
    public function onSetNodeOffline(SetNodeOfflineEvent $event)
    {
        $node = $event->getNode();
        $language = $event->getLanguage();

        $this->lookupBuilder->removeOnlineByTreeNodeAndLanguage($node, $language);
    }

    /**
     * @param NodeEvent $event
     */
    public function onDeleteNode(NodeEvent $event)
    {
        $node = $event->getNode();

        $this->lookupBuilder->remove($node);
    }
}
