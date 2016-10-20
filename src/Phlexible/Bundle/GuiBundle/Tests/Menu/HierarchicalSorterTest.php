<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Menu;

use Phlexible\Bundle\GuiBundle\Menu\HierarchicalSorter;
use Phlexible\Bundle\GuiBundle\Menu\MenuItem;
use Phlexible\Bundle\GuiBundle\Menu\MenuItemCollection;

/**
 * Hierarchy builder test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class HierarchicalSorterTest extends \PHPUnit_Framework_TestCase
{
    public function testSort()
    {
        $items = new MenuItemCollection();
        $items->set('main', new MenuItem('main'));
        $items->set('sub', new MenuItem('sub', 'main'));

        $sorter = new HierarchicalSorter();
        $hierarchy = $sorter->sort($items);

        $this->assertArrayHasKey('main', $hierarchy->getItems());
        $this->assertArrayHasKey('sub', $hierarchy->getItems()['main']->getItems()->getItems());
    }
}
