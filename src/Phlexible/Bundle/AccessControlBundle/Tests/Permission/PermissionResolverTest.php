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
use Phlexible\Bundle\AccessControlBundle\Permission\PermissionResolver;

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

    public function setUp()
    {
        $this->permissions = new PermissionCollection(
            array(
                new Permission('test', 'read', 1, 'read-icon'),
                new Permission('test', 'create', 2, 'create-icon'),
                new Permission('test', 'update', 4, 'update-icon'),
                new Permission('test', 'delete', 8, 'delete-icon'),
            )
        );
        $this->resolver = new PermissionResolver($this->permissions);
    }

    public function testResolve()
    {
        $permissions = $this->resolver->resolve('test', 1);
        $this->assertCount(1, $permissions);
        $this->assertSame(1, $permissions[0]->getBit());

        $permissions = $this->resolver->resolve('test', 2);
        $this->assertCount(1, $permissions);
        $this->assertSame(2, $permissions[0]->getBit());

        $permissions = $this->resolver->resolve('test', 4 | 8);
        $this->assertCount(2, $permissions);
        $this->assertSame(4, $permissions[0]->getBit());
        $this->assertSame(8, $permissions[1]->getBit());

        $permissions = $this->resolver->resolve('test', 1 | 2 | 8);
        $this->assertCount(3, $permissions);
        $this->assertSame(1, $permissions[0]->getBit());
        $this->assertSame(2, $permissions[1]->getBit());
        $this->assertSame(8, $permissions[2]->getBit());
    }

    /**
     * @expectedException \Phlexible\Bundle\AccessControlBundle\Exception\InvalidArgumentException
     */
    public function testResolveThrowsInvalidArgumentExceptionOnUnknownBit()
    {
        $this->resolver->resolve('test', 16 | 64);
    }
}
