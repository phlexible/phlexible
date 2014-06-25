<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Menu;

use Phlexible\Component\ComponentCollection;
use Phlexible\Bundle\GuiBundle\Event\GetMenuEvent;
use Phlexible\Bundle\GuiBundle\GuiEvents;
use Phlexible\Bundle\GuiBundle\Menu\Loader\DelegatingLoader;
use Phlexible\Bundle\GuiBundle\Menu\Loader\LoaderResolver;
use Phlexible\Bundle\GuiBundle\Menu\Loader\YamlFileLoader;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;
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
     * @param array $bundles
     *
     * @return MenuItemCollection
     */
    public function load(array $bundles)
    {
        $loader = new DelegatingLoader(
            new LoaderResolver(
                array (
                    new YamlFileLoader(),
                )
            )
        );
        $items = new MenuItemCollection();
        foreach ($bundles as $class) {
            $reflection = new \ReflectionClass($class);
            $path = dirname($reflection->getFileName());

            $configDir = $path . '/Resources/config/';
            if (file_exists($configDir)) {
                $finder = new Finder();
                foreach ($finder->in($configDir)->name('menuhandles.*') as $file) {
                    $filename = $file->getPathName();
                    $loadedItems = $loader->load($filename);
                    $items->merge($loadedItems);
                }
            }
            /*
            elseif (method_exists($component, 'getMainViews')) {
                $componentRoutes = $component->getRoutes();
                $resources[] = $component->getFileResource();
                $routes = array_merge($routes, $componentRoutes);
            }
            */
        }

        $event = new GetMenuEvent($items);
        $this->dispatcher->dispatch(GuiEvents::GET_MENU, $event);

        $sorter = new HierarchicalSorter();
        $items = $sorter->sort($items);

        return $items;
    }
}