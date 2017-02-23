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

use Phlexible\Component\AccessControl\Permission\PermissionCollection;
use Phlexible\Component\AccessControl\Permission\PermissionProviderInterface;
use Phlexible\Component\AccessControl\Permission\PermissionRegistry;

class TestPermissionProvider implements PermissionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return new PermissionCollection('collection1');
    }
}
/**
 * Permission registry test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PermissionRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testAddStoresPermissionCollections()
    {
        $registry = new PermissionRegistry();
        $registry->add($type1 = new PermissionCollection('testType1'));
        $registry->add($type2 = new PermissionCollection('testType2'));

        $this->assertCount(2, $registry->all());
        $this->assertTrue($registry->has('testType1'));
        $this->assertTrue($registry->has('testType2'));
        $this->assertFalse($registry->has('invalidType'));
        $this->assertSame($type1, $registry->get('testType1'));
        $this->assertSame($type2, $registry->get('testType2'));
    }

    /**
     * @expectedException \Phlexible\Component\AccessControl\Exception\InvalidArgumentException
     */
    public function testGetOnInvalidObjectTypeThrowsException()
    {
        $registry = new PermissionRegistry();
        $registry->get('invalidType');
    }

    public function testAddProviderAddsPermissionCollections()
    {
        $registry = new PermissionRegistry();
        $registry->addProvider(new TestPermissionProvider());

        $this->assertCount(1, $registry->all());
        $this->assertTrue($registry->has('collection1'));
    }
}
