<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Menu;

/**
 * Hierarchy builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class HierarchicalSorter
{
    /**
     * @param MenuItemCollection $items
     *
     * @return MenuItemCollection
     */
    public function sort(MenuItemCollection $items)
    {
        $filteredItems = $this->filterParent(null, $items);

        return $filteredItems;
    }

    /**
     * Filter handlers by parent name
     *
     * @param string             $parent
     * @param MenuItemCollection $items
     *
     * @return MenuItemCollection
     */
    private function filterParent($parent, MenuItemCollection $items)
    {
        $filteredItems = new MenuItemCollection();

        foreach ($items->getItems() as $name => $item) {
            if ($parent === $item->getParent()) {
                $subItems = $this->filterParent($name, $items);
                if (count($subItems)) {
                    $item->setItems($subItems);
                }
                $filteredItems->set($name, $item);
                $items->remove($name);
            }
        }

        return $filteredItems;
    }
}
