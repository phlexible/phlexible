<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\TreeBundle\Event\DeleteNodeEvent;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Tree listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeListener implements EventSubscriberInterface
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @param ElementService $elementService
     */
    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
    }

    /**
     * @param DeleteNodeEvent $event
     */
    public function onDeleteNode(DeleteNodeEvent $event)
    {
        $node = $event->getNode();

        $tree = $node->getTree();
        $isInstance = $tree->isInstance($node);

        if ($isInstance) {
            return;
        }

        $element = $this->elementService->findElement($node->getTypeId());
        $this->elementService->deleteElement($element);
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
