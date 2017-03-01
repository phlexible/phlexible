<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Menu;

/**
 * Menu item collection.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MenuItemCollection implements \Countable
{
    /**
     * @var MenuItem[]
     */
    private $items = array();

    /**
     * @param string   $name
     * @param MenuItem $item
     *
     * @return $this
     */
    public function set($name, MenuItem $item)
    {
        $this->items[$name] = $item;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function remove($name)
    {
        unset($this->items[$name]);

        return $this;
    }

    /**
     * @param MenuItemCollection $items
     *
     * @return $this
     */
    public function merge(MenuItemCollection $items)
    {
        foreach ($items->getItems() as $name => $item) {
            $this->set($name, $item);
        }

        return $this;
    }

    /**
     * @return MenuItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = array();

        foreach ($this->items as $name => $item) {
            $itemData = array(
                'name' => $name,
                'handle' => $item->getHandle(),
            );

            if (count($item->getItems())) {
                $itemData['menu'] = $item->getItems()->toArray();
            }

            if (count($item->getParameters())) {
                $itemData['parameters'] = $item->getParameters();
            }

            if (count($item->getRoles())) {
                $itemData['roles'] = $item->getRoles();
            }

            $data[] = $itemData;
        }

        return $data;
    }
}
