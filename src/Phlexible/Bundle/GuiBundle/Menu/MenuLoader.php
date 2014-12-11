<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Menu;

use Phlexible\Bundle\GuiBundle\Event\GetMenuEvent;
use Phlexible\Bundle\GuiBundle\GuiEvents;
use Phlexible\Bundle\GuiBundle\Menu\Loader\DelegatingLoader;
use Phlexible\Bundle\GuiBundle\Menu\Loader\LoaderResolver;
use Phlexible\Bundle\GuiBundle\Menu\Loader\YamlFileLoader;
use Phlexible\Component\ComponentCollection;
use Puli\Repository\ResourceRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;

/**
 * Menu loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MenuLoader
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param ResourceRepositoryInterface $repository
     *
     * @return MenuItemCollection
     */
    public function load(ResourceRepositoryInterface $repository)
    {
        $loader = new DelegatingLoader(
            new LoaderResolver(
                [
                    new YamlFileLoader(),
                ]
            )
        );
        $items = new MenuItemCollection();
        foreach ($repository->find('/phlexible/menu/*/*') as $resource) {
            /* @var $resource \Puli\Repository\Filesystem\Resource\LocalFileResource */

            $loadedItems = $loader->load($resource->getLocalPath());
            $items->merge($loadedItems);
        }

        $event = new GetMenuEvent($items);
        $this->dispatcher->dispatch(GuiEvents::GET_MENU, $event);

        $sorter = new HierarchicalSorter();
        $items = $sorter->sort($items);

        return $items;
    }
}
