<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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