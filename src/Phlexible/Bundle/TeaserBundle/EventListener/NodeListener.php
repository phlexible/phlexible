<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\EventListener;

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

    public function onPublishNode(NodeEvent $event, array $params)
    {
        /* @var $container MWF_Container_ContainerInterface */
        $container = $params['container'];

        $node = $event->getNode();
        $eid  = $node->getEid();

        $catchHelper = $container->get('phlexible_teaser.catch.helper');
        $catchHelper->updateOnline($eid);
    }

    public function onSetNodeOffline(NodeEvent $event, array $params)
    {
        /* @var $container MWF_Container_ContainerInterface */
        $container = $params['container'];

        $node     = $event->getNode();
        $tid      = $node->getId();
        $language = $event->getLanguage();

        $catchHelper = $container->get('phlexible_teaser.catch.helper');
        $catchHelper->removeOnlineByTidAndLanguage($tid, $language);
    }

    public function onDeleteNode(NodeEvent $event, array $params)
    {
        /* @var $container MWF_Container_ContainerInterface */
        $container = $params['container'];

        $node = $event->getNode();
        $tid  = $node->getId();

        $catchHelper = $container->get('phlexible_teaser.catch.helper');
        $catchHelper->removeByTid($tid);
    }

    public function onUpdateNode(NodeEvent $event, array $params)
    {
        return;
        /* @var $container MWF_Container_ContainerInterface */
        $container = $params['container'];

        $node = $event->getNode();
        $eid  = $node->getEid();

        $catchHelper = $container->get('phlexible_teaser.catch.helper');
        $catchHelper->updatePreview($eid);
    }

    public function onCreateNodeInstance(NodeEvent $event, array $params)
    {
        /* @var $container MWF_Container_ContainerInterface */
        $container = $params['container'];

        $node = $event->getNode();
        $eid  = $node->getEid();

        $catchHelper = $container->get('phlexible_teaser.catch.helper');
        $catchHelper->updatePreview($eid);
    }
}
