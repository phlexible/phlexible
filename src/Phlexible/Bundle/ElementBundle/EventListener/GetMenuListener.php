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
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Get menu listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetMenuListener
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @var TreeManager
     */
    private $treeManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var array
     */
    private $availableLanguages;

    /**
     * @param SiterootManagerInterface      $siterootManager
     * @param TreeManager                   $treeManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param string                        $availableLanguages
     */
    public function __construct(
        SiterootManagerInterface $siterootManager,
        TreeManager $treeManager,
        AuthorizationCheckerInterface $authorizationChecker,
        $availableLanguages
    ) {
        $this->siterootManager = $siterootManager;
        $this->treeManager = $treeManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->availableLanguages = explode(',', $availableLanguages);
    }

    /**
     * @param GetMenuEvent $event
     */
    public function onGetMenu(GetMenuEvent $event)
    {
        $items = $event->getItems();

        foreach ($this->siterootManager->findAll() as $siteroot) {
            $tree = $this->treeManager->getBySiterootId($siteroot->getId());
            $root = $tree->getRoot();

            $siterootLanguages = array();
            foreach ($this->availableLanguages as $language) {
                if ($this->authorizationChecker->isGranted(['permission' => 'VIEW', 'language' => $language], $root)) {
                    $siterootLanguages[$siteroot->getId()][] = $language;
                }
            }

            if (!count($siterootLanguages)) {
                continue;
            }

            $menuItem = new MenuItem('element', 'elements');
            $menuItem->setParameters(
                [
                    'siteroot_id' => $siteroot->getId(),
                    'title'       => $siteroot->getTitle(),
                ]
            );

            $items->set('siteroot_' . $siteroot->getId(), $menuItem);
        }
    }
}
