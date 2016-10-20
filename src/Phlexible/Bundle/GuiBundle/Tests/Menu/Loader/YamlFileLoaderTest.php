<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Menu\Loader;

use org\bovigo\vfs\vfsStream;
use Phlexible\Bundle\GuiBundle\Menu\Loader\YamlFileLoader;

/**
 * YAML file loader test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class YamlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testSupports()
    {
        $loader = new YamlFileLoader();

        $this->assertTrue($loader->supports('test.yml'));
        $this->assertFalse($loader->supports('test.xml'));
    }

    public function testLoad()
    {
        $items = <<<EOF
menus:
    handle: menus

config:
    parent: menus
    handle: configuration
    roles: [a, b]
EOF;

        vfsStream::setup('root', null, array('items.yml' => $items));

        $loader = new YamlFileLoader();
        $items = $loader->load(vfsStream::url('root/items.yml'));

        $this->assertCount(2, $items);
        $this->assertArrayHasKey('menus', $items->getItems());
        $this->assertSame('menus', $items->getItems()['menus']->getHandle());
        $this->assertArrayHasKey('config', $items->getItems());
        $this->assertSame('configuration', $items->getItems()['config']->getHandle());
        $this->assertSame('menus', $items->getItems()['config']->getParent());
        $this->assertSame(array('a', 'b'), $items->getItems()['config']->getRoles());
    }

    /**
     * @expectedException \Phlexible\Bundle\GuiBundle\Menu\Loader\LoaderException
     */
    public function testLoadWithInvalidParent()
    {
        $items = <<<EOF
config:
    parent: 123
    handle: configuration
EOF;

        vfsStream::setup('root', null, array('items.yml' => $items));

        $loader = new YamlFileLoader();
        $items = $loader->load(vfsStream::url('root/items.yml'));

    }

    /**
     * @expectedException \Phlexible\Bundle\GuiBundle\Menu\Loader\LoaderException
     */
    public function testLoadWithMissingHandler()
    {
        $items = <<<EOF
menus:
EOF;

        vfsStream::setup('root', null, array('items.yml' => $items));

        $loader = new YamlFileLoader();
        $loader->load(vfsStream::url('root/items.yml'));
    }

    /**
     * @expectedException \Phlexible\Bundle\GuiBundle\Menu\Loader\LoaderException
     */
    public function testLoadWithInvalidHandler()
    {
        $items = <<<EOF
menus:
    test: 123
EOF;

        vfsStream::setup('root', null, array('items.yml' => $items));

        $loader = new YamlFileLoader();
        $loader->load(vfsStream::url('root/items.yml'));
    }

    /**
     * @expectedException \Phlexible\Bundle\GuiBundle\Menu\Loader\LoaderException
     */
    public function testLoadWithInvalidRoles()
    {
        $items = <<<EOF
menus:
    roles: abc
EOF;

        vfsStream::setup('root', null, array('items.yml' => $items));

        $loader = new YamlFileLoader();
        $loader->load(vfsStream::url('root/items.yml'));
    }
}
