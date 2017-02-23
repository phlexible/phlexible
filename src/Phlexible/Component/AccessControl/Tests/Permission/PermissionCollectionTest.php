<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\Tests\Permission;

use Phlexible\Component\AccessControl\Permission\Permission;
use Phlexible\Component\AccessControl\Permission\PermissionCollection;

/**
 * Permission collection test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PermissionCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddPermission()
    {
        $permissions = new PermissionCollection('testType');
        $permissions->add($permission1 = new Permission('foo', 1));
        $permissions->add($permission2 = new Permission('bar', 2));
        $permissions->add($permission3 = new Permission('baz', 4));

        $this->assertCount(3, $permissions->all());
        $this->assertTrue($permissions->has('foo'));
        $this->assertTrue($permissions->has('bar'));
        $this->assertTrue($permissions->has('baz'));
        $this->assertSame($permission1, $permissions->get('foo'));
        $this->assertSame($permission2, $permissions->get('bar'));
        $this->assertSame($permission3, $permissions->get('baz'));
    }

    /**
     * @expectedException \Phlexible\Component\AccessControl\Exception\InvalidArgumentException
     */
    public function testAddDuplicateBitInPermissionThrowsInvalidArgumentException()
    {
        $permissions = new PermissionCollection('testType');
        $permissions->add(new Permission('foo', 1));
        $permissions->add(new Permission('bar', 1));
    }

    /**
     * @expectedException \Phlexible\Component\AccessControl\Exception\InvalidArgumentException
     */
    public function testAddDuplicateNameInPermissionThrowsInvalidArgumentException()
    {
        $permissions = new PermissionCollection('testType');
        $permissions->add(new Permission('foo', 1));
        $permissions->add(new Permission('foo', 2));
    }

    public function testAddCollection()
    {
        $addPermissions = new PermissionCollection('testType');
        $addPermissions->add($permission1 = new Permission('foo', 1));
        $addPermissions->add($permission2 = new Permission('bar', 2));
        $addPermissions->add($permission3 = new Permission('baz', 4));

        $permissions = new PermissionCollection('testType');
        $permissions->addCollection($addPermissions);

        $this->assertCount(3, $permissions->all());
        $this->assertTrue($permissions->has('foo'));
        $this->assertTrue($permissions->has('bar'));
        $this->assertTrue($permissions->has('baz'));
        $this->assertSame($permission1, $permissions->get('foo'));
        $this->assertSame($permission2, $permissions->get('bar'));
        $this->assertSame($permission3, $permissions->get('baz'));
    }

    /**
     * @expectedException \Phlexible\Component\AccessControl\Exception\InvalidArgumentException
     */
    public function testAddCollectionWithMismatchingObjectTypeThrowsIvalidArgumentException()
    {
        $addPermissions = new PermissionCollection('testType1');

        $permissions = new PermissionCollection('testType2');
        $permissions->addCollection($addPermissions);
    }
}
