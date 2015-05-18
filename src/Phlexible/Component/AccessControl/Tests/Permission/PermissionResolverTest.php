<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Tests\Permission;

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
     * @var \Phlexible\Component\AccessControl\Permission\PermissionCollection
     */
    private $permissions;

    public function setUp()
    {
        $this->permissions = new PermissionCollection(
            [
                new \Phlexible\Component\AccessControl\Permission\Permission('contentClass', 'read', 1, 'read-icon'),
                new \Phlexible\Component\AccessControl\Permission\Permission('contentClass', 'create', 2, 'create-icon'),
                new \Phlexible\Component\AccessControl\Permission\Permission('contentClass', 'update', 4, 'update-icon'),
                new \Phlexible\Component\AccessControl\Permission\Permission('contentClass', 'delete', 8, 'delete-icon'),
            ]
        );
        $this->resolver = new PermissionResolver($this->permissions);
    }

    public function testResolve()
    {
        $permissions = $this->resolver->resolve('contentClass', 1);
        $this->assertCount(1, $permissions);
        $this->assertSame(1, $permissions[0]->getBit());

        $permissions = $this->resolver->resolve('contentClass', 2);
        $this->assertCount(1, $permissions);
        $this->assertSame(2, $permissions[0]->getBit());

        $permissions = $this->resolver->resolve('contentClass', 4 | 8);
        $this->assertCount(2, $permissions);
        $this->assertSame(4, $permissions[0]->getBit());
        $this->assertSame(8, $permissions[1]->getBit());

        $permissions = $this->resolver->resolve('contentClass', 1 | 2 | 8);
        $this->assertCount(3, $permissions);
        $this->assertSame(1, $permissions[0]->getBit());
        $this->assertSame(2, $permissions[1]->getBit());
        $this->assertSame(8, $permissions[2]->getBit());
    }

    /**
     * @expectedException \Phlexible\Component\AccessControl\Exception\InvalidArgumentException
     */
    public function testResolveThrowsInvalidArgumentExceptionOnUnknownBit()
    {
        $this->resolver->resolve('test', 16 | 64);
    }
}
