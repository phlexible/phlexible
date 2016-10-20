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
use Phlexible\Component\AccessControl\Permission\PermissionResolver;

/**
 * Permission resolver test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PermissionResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PermissionResolver
     */
    private $resolver;

    /**
     * @var PermissionCollection
     */
    private $permissions;

    public function createPermissions()
    {
        return new PermissionCollection(
            'type1',
            array(
                new Permission('read', 1),
                new Permission('create', 2),
                new Permission('update', 4),
                new Permission('delete', 8),
            )
        );

    }

    public function testResolve()
    {
        $resolver = new PermissionResolver();
        $permissions = $this->createPermissions();

        $resolvedPermissions = $resolver->resolve($permissions, 1);
        $this->assertCount(1, $resolvedPermissions);
        $this->assertSame(1, $resolvedPermissions[0]->getBit());

        $resolvedPermissions = $resolver->resolve($permissions, 2);
        $this->assertCount(1, $resolvedPermissions);
        $this->assertSame(2, $resolvedPermissions[0]->getBit());

        $resolvedPermissions = $resolver->resolve($permissions, 4 | 8);
        $this->assertCount(2, $resolvedPermissions);
        $this->assertSame(4, $resolvedPermissions[0]->getBit());
        $this->assertSame(8, $resolvedPermissions[1]->getBit());

        $resolvedPermissions = $resolver->resolve($permissions, 1 | 2 | 8);
        $this->assertCount(3, $resolvedPermissions);
        $this->assertSame(1, $resolvedPermissions[0]->getBit());
        $this->assertSame(2, $resolvedPermissions[1]->getBit());
        $this->assertSame(8, $resolvedPermissions[2]->getBit());
    }

    /**
     * @expectedException \Phlexible\Component\AccessControl\Exception\InvalidArgumentException
     */
    public function testResolveThrowsInvalidArgumentExceptionOnUnknownBit()
    {
        $resolver = new PermissionResolver();
        $permissions = $this->createPermissions();

        $resolver->resolve($permissions, 16 | 64);
    }
}
