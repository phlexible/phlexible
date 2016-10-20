<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Event;

use Phlexible\Bundle\GuiBundle\Menu\MenuItemCollection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Get menu event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetMenuEvent extends Event
{
    /**
     * @var MenuItemCollection
     */
    private $items;

    /**
     * @param MenuItemCollection $items
     */
    public function __construct(MenuItemCollection $items)
    {
        $this->items = $items;
    }

    /**
     * @return MenuItemCollection
     */
    public function getItems()
    {
        return $this->items;
    }
}
