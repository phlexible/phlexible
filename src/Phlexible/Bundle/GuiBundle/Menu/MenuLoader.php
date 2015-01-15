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
use Puli\Discovery\Api\ResourceDiscovery;
use Puli\Repository\Api\ResourceRepository;
use Puli\Repository\Resource\FileResource;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Menu loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MenuLoader
{
    /**
     * @var ResourceDiscovery
     */
    private $puliDiscovery;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param ResourceDiscovery        $puliDiscovery
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(ResourceDiscovery $puliDiscovery, EventDispatcherInterface $dispatcher)
    {
        $this->puliDiscovery = $puliDiscovery;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return MenuItemCollection
     */
    public function load()
    {
        $loader = new DelegatingLoader(
            new LoaderResolver(
                [
                    new YamlFileLoader(),
                ]
            )
        );
        $items = new MenuItemCollection();
        foreach ($this->puliDiscovery->find('phlexible/menu') as $resource) {
            /* @var $resource FileResource */

            $loadedItems = $loader->load($resource->getFilesystemPath());
            $items->merge($loadedItems);
        }

        $event = new GetMenuEvent($items);
        $this->dispatcher->dispatch(GuiEvents::GET_MENU, $event);

        $sorter = new HierarchicalSorter();
        $items = $sorter->sort($items);

        return $items;
    }
}
