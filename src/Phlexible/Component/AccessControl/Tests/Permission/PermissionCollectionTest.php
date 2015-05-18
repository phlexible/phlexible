<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Tests\Permission;

use Phlexible\Component\AccessControl\Permission\Permission;
use Phlexible\Component\AccessControl\Permission\PermissionCollection;

/**
 * Permission collection test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PermissionCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddPermission()
    {
        $permissions = new PermissionCollection();
        $permissions->add(new \Phlexible\Component\AccessControl\Permission\Permission('contentClass', 'testPermission1', 1, 'test-icon'));
        $permissions->add(new \Phlexible\Component\AccessControl\Permission\Permission('contentClass', 'testPermission2', 2, 'test-icon'));
        $permissions->add(new \Phlexible\Component\AccessControl\Permission\Permission('anotherContentClass', 'testPermission3', 4, 'test-icon'));

        $this->assertSame(3, count($permissions->getAll()));
        $this->assertSame(2, count($permissions->getByContentClass('contentClass')));
        $this->assertSame(1, count($permissions->getByContentClass('anotherContentClass')));
    }

    /**
     * @expectedException \Phlexible\Component\AccessControl\Exception\InvalidArgumentException
     */
    public function testAddDuplicateBitInPermissionThrowsInvalidArgumentException()
    {
        $permissions = new \Phlexible\Component\AccessControl\Permission\PermissionCollection();
        $permissions->add(new Permission('contentClass', 'testPermission', 1, 'test-icon'));
        $permissions->add(new \Phlexible\Component\AccessControl\Permission\Permission('contentClass', 'anotherTestPermission', 1, 'test-icon'));
    }

    /**
     * @expectedException \Phlexible\Component\AccessControl\Exception\InvalidArgumentException
     */
    public function testAddDuplicateNameInPermissionThrowsInvalidArgumentException()
    {
        $permissions = new \Phlexible\Component\AccessControl\Permission\PermissionCollection();
        $permissions->add(new Permission('contentClass', 'testPermission', 1, 'test-icon'));
        $permissions->add(new \Phlexible\Component\AccessControl\Permission\Permission('contentClass', 'testPermission', 2, 'test-icon'));
    }

    public function testAddCollection()
    {
        $addPermissions = new PermissionCollection();
        $addPermissions->add(new \Phlexible\Component\AccessControl\Permission\Permission('contentClass', 'testPermission1', 1, 'test-icon'));
        $addPermissions->add(new \Phlexible\Component\AccessControl\Permission\Permission('contentClass', 'testPermission2', 2, 'test-icon'));
        $addPermissions->add(new \Phlexible\Component\AccessControl\Permission\Permission('anotherContentClass', 'testPermission3', 4, 'test-icon'));

        $permissions = new \Phlexible\Component\AccessControl\Permission\PermissionCollection();
        $permissions->addCollection($addPermissions);

        $this->assertSame(3, count($permissions->getAll()));
        $this->assertSame(2, count($permissions->getByContentClass('contentClass')));
        $this->assertSame(1, count($permissions->getByContentClass('anotherContentClass')));
    }

}
