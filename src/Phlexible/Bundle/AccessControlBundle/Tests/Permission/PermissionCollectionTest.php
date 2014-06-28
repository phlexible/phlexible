<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Tests\Permission;

use Phlexible\Bundle\AccessControlBundle\Permission\Permission;
use Phlexible\Bundle\AccessControlBundle\Permission\PermissionCollection;

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
        $permissions->add(new Permission('test', 'testPermission1', 1, 'test-icon'));
        $permissions->add(new Permission('test', 'testPermission2', 2, 'test-icon'));
        $permissions->add(new Permission('test', 'testPermission3', 4, 'test-icon'));

        $this->assertSame(3, count($permissions->getAll()));
        $this->assertSame(3, count($permissions->getByType('test')));
    }

    /**
     * @expectedException \Phlexible\Bundle\AccessControlBundle\Exception\InvalidArgumentException
     */
    public function testAddDuplicateBitInPermissionThrowsInvalidArgumentException()
    {
        $permissions = new PermissionCollection();
        $permissions->add(new Permission('test', 'testPermission', 1, 'test-icon'));
        $permissions->add(new Permission('test', 'anotherTestPermission', 1, 'test-icon'));
    }

    /**
     * @expectedException \Phlexible\Bundle\AccessControlBundle\Exception\InvalidArgumentException
     */
    public function testAddDuplicateNameInPermissionThrowsInvalidArgumentException()
    {
        $permissions = new PermissionCollection();
        $permissions->add(new Permission('test', 'testPermission', 1, 'test-icon'));
        $permissions->add(new Permission('test', 'testPermission', 2, 'test-icon'));
    }

    public function testAddCollection()
    {
        $addPermissions = new PermissionCollection();
        $addPermissions->add(new Permission('test', 'testPermission1', 1, 'test-icon'));
        $addPermissions->add(new Permission('test', 'testPermission2', 2, 'test-icon'));
        $addPermissions->add(new Permission('test', 'testPermission3', 4, 'test-icon'));

        $permissions = new PermissionCollection();
        $permissions->addCollection($addPermissions);

        $this->assertSame(3, count($permissions->getAll()));
        $this->assertSame(3, count($permissions->getByType('test')));
    }

}
