<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\EventListener;

use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TreeBundle\Event\DeleteNodeEvent;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Tree listener.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeListener implements EventSubscriberInterface
{
    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @param TeaserManagerInterface $teaserManager
     */
    public function __construct(TeaserManagerInterface $teaserManager)
    {
        $this->teaserManager = $teaserManager;
    }

    /**
     * @param DeleteNodeEvent $event
     */
    public function onDeleteNode(DeleteNodeEvent $event)
    {
        $nodeId = $event->getNodeId();
        $userId = $event->getUserId();

        $teasers = $this->teaserManager->findBy(
            array('treeId' => $nodeId)
        );

        foreach ($teasers as $teaser) {
            $this->teaserManager->deleteTeaser($teaser, $userId);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            TreeEvents::DELETE_NODE => 'onDeleteNode',
        );
    }
}
