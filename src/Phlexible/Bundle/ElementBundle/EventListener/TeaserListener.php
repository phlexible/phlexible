<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\TeaserBundle\Event\DeleteTeaserEvent;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TeaserBundle\TeaserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Teaser listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserListener implements EventSubscriberInterface
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @param ElementService         $elementService
     * @param TeaserManagerInterface $teaserManager
     */
    public function __construct(ElementService $elementService, TeaserManagerInterface $teaserManager)
    {
        $this->elementService = $elementService;
        $this->teaserManager = $teaserManager;
    }

    /**
     * @param DeleteTeaserEvent $event
     */
    public function onDeleteTeaser(DeleteTeaserEvent $event)
    {
        $teaser = $event->getTeaser();

        $isInstance = $this->teaserManager->isInstance($teaser);

        if ($isInstance) {
            return;
        }

        $element = $this->elementService->findElement($teaser->getTypeId());
        $this->elementService->deleteElement($element);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            TeaserEvents::DELETE_TEASER => 'onDeleteTeaser',
        );
    }
}
