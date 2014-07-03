<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\GuiBundle\Event\GetMenuEvent;
use Phlexible\Bundle\GuiBundle\Menu\MenuItem;

/**
 * Get menu listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetMenuListener
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param GetMenuEvent $event
     */
    public function onGetMenu(GetMenuEvent $event)
    {
        $items = $event->getItems();

        $siteroots = $this->entityManager->getRepository('PhlexibleSiterootBundle:Siteroot')->findAll();

        foreach ($siteroots as $siteroot) {
            $menuItem = new MenuItem('Phlexible.elements.menuhandle.ElementHandle', 'elements');
            $menuItem->setParameters(
                array(
                    'siteroot_id' => $siteroot->getId(),
                    'title'       => $siteroot->getTitle(),
                )
            );

            $items->set('siteroot_' . $siteroot->getId(), $menuItem);

        }
    }
}
