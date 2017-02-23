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

use Phlexible\Component\AccessControl\Domain\AccessControlList;
use Phlexible\Component\AccessControl\Domain\Entry;
use Phlexible\Component\AccessControl\Permission\HierarchyMaskResolver;
use PHPUnit\Framework\TestCase;

/**
 * Hierarchy mask resolver test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @covers \Phlexible\Component\AccessControl\Permission\HierarchyMaskResolver
 */
class HierarchyMaskResolverTest extends TestCase
{
    const READ = 1;
    const WRITE = 2;
    const DELETE = 4;
    const NUKE = 8;
    const ADMIN = 16;

    /**
     * @var HierarchyMaskResolver
     */
    private $resolver;

    public function setUp()
    {
        $this->resolver = new HierarchyMaskResolver();
    }

    public function testSingleMask()
    {
        $acl = $this->prophesize(AccessControlList::class);

        $ace = new Entry($acl->reveal(), 'object', 1, 'security', 2, self::READ | self::WRITE, null, null);

        $result = $this->resolver->resolve(array($ace), 1);

        $this->assertEquals(self::READ | self::WRITE, $result['effectiveMask']);
        $this->assertEquals(self::READ | self::WRITE, $result['mask']);
        $this->assertNull($result['stopMask']);
        $this->assertNull($result['noInheritMask']);
        $this->assertNull($result['parentMask']);
        $this->assertNull($result['parentStopMask']);
        $this->assertNull($result['parentNoInheritMask']);
    }

    public function testTwoLevels()
    {
        $acl = $this->prophesize(AccessControlList::class);

        $ace1 = new Entry($acl->reveal(), 'object', 1, 'security', 2, self::READ | self::WRITE, null, null);
        $ace2 = new Entry($acl->reveal(), 'object', 2, 'security', 2, self::DELETE, null, null);

        $result = $this->resolver->resolve(array($ace1, $ace2), 2);

        $this->assertEquals(self::READ | self::WRITE | self::DELETE, $result['effectiveMask']);
        $this->assertEquals(self::DELETE, $result['mask']);
        $this->assertNull($result['stopMask']);
        $this->assertNull($result['noInheritMask']);
        $this->assertEquals(self::READ | self::WRITE, $result['parentMask']);
        $this->assertNull($result['parentStopMask']);
        $this->assertNull($result['parentNoInheritMask']);
    }

    public function testTwoLevelsWithStopMask()
    {
        $acl = $this->prophesize(AccessControlList::class);

        $ace1 = new Entry($acl->reveal(), 'object', 1, 'security', 2, self::READ | self::WRITE, null, null);
        $ace2 = new Entry($acl->reveal(), 'object', 2, 'security', 2, self::DELETE, self::READ, null);

        $result = $this->resolver->resolve(array($ace1, $ace2), 2);

        $this->assertEquals(self::WRITE | self::DELETE, $result['effectiveMask']);
        $this->assertEquals(self::DELETE, $result['mask']);
        $this->assertEquals(self::READ, $result['stopMask']);
        $this->assertNull($result['noInheritMask']);
        $this->assertEquals(self::READ | self::WRITE, $result['parentMask']);
        $this->assertNull($result['parentStopMask']);
        $this->assertNull($result['parentNoInheritMask']);
    }

    public function testTwoLevelsWithNoInheritMask()
    {
        $acl = $this->prophesize(AccessControlList::class);

        $ace1 = new Entry($acl->reveal(), 'object', 1, 'security', 2, self::READ | self::WRITE, null, self::WRITE);
        $ace2 = new Entry($acl->reveal(), 'object', 2, 'security', 2, self::DELETE, null, null);

        $result = $this->resolver->resolve(array($ace1, $ace2), 2);

        $this->assertEquals(self::READ | self::DELETE, $result['effectiveMask']);

        $this->assertEquals(self::DELETE, $result['mask']);
        $this->assertNull($result['stopMask']);
        $this->assertNull($result['noInheritMask']);

        $this->assertEquals(self::READ | self::WRITE, $result['parentMask']);
        $this->assertNull($result['parentStopMask']);
        $this->assertEquals(self::WRITE, $result['parentNoInheritMask']);
    }

    public function testTwoLevelsWithStopMaskAndNoInheritMask()
    {
        $acl = $this->prophesize(AccessControlList::class);

        $ace1 = new Entry($acl->reveal(), 'object', 1, 'security', 2, self::READ | self::WRITE, null, self::WRITE);
        $ace2 = new Entry($acl->reveal(), 'object', 2, 'security', 2, self::DELETE, self::READ, null);

        $result = $this->resolver->resolve(array($ace1, $ace2), 2);

        $this->assertEquals(self::DELETE, $result['effectiveMask']);
        $this->assertEquals(self::DELETE, $result['mask']);
        $this->assertEquals(self::READ, $result['stopMask']);
        $this->assertNull($result['noInheritMask']);
        $this->assertEquals(self::READ | self::WRITE, $result['parentMask']);
        $this->assertNull($result['parentStopMask']);
        $this->assertEquals(self::WRITE, $result['parentNoInheritMask']);
    }

    public function testThreeLevels()
    {
        $acl = $this->prophesize(AccessControlList::class);

        $ace1 = new Entry($acl->reveal(), 'object', 1, 'security', 2, self::READ | self::WRITE, null, null);
        $ace2 = new Entry($acl->reveal(), 'object', 2, 'security', 2, self::DELETE, null, null);
        $ace3 = new Entry($acl->reveal(), 'object', 3, 'security', 2, self::NUKE, null, null);

        $result = $this->resolver->resolve(array($ace1, $ace2, $ace3), 3);

        $this->assertEquals(self::READ | self::WRITE | self::DELETE | self::NUKE, $result['effectiveMask']);
        $this->assertEquals(self::NUKE, $result['mask']);
        $this->assertNull($result['stopMask']);
        $this->assertNull($result['noInheritMask']);
        $this->assertEquals(self::READ | self::WRITE | self::DELETE, $result['parentMask']);
        $this->assertNull($result['parentStopMask']);
        $this->assertNull($result['parentNoInheritMask']);
    }

    public function testThreeLevelsWithStopMask()
    {
        $acl = $this->prophesize(AccessControlList::class);

        $ace1 = new Entry($acl->reveal(), 'object', 1, 'security', 2, self::READ | self::WRITE, null, null);
        $ace2 = new Entry($acl->reveal(), 'object', 2, 'security', 2, self::DELETE, self::READ, null);
        $ace3 = new Entry($acl->reveal(), 'object', 3, 'security', 2, self::NUKE, self::DELETE, null);

        $result = $this->resolver->resolve(array($ace1, $ace2, $ace3), 3);

        $this->assertEquals(self::WRITE | self::NUKE, $result['effectiveMask']);

        $this->assertEquals(self::WRITE | self::NUKE, $result['effectiveMask']);
        $this->assertEquals(self::NUKE, $result['mask']);
        $this->assertEquals(self::DELETE, $result['stopMask']);
        $this->assertNull($result['noInheritMask']);
        $this->assertEquals(self::WRITE | self::DELETE, $result['parentMask']);
        $this->assertEquals(self::READ, $result['parentStopMask']);
        $this->assertNull($result['parentNoInheritMask']);
    }

    public function testThreeLevelsWithNoInheritMask()
    {
        $acl = $this->prophesize(AccessControlList::class);

        $ace1 = new Entry($acl->reveal(), 'object', 1, 'security', 2, self::READ | self::WRITE, null, self::READ);
        $ace2 = new Entry($acl->reveal(), 'object', 2, 'security', 2, self::DELETE, null, self::WRITE);
        $ace3 = new Entry($acl->reveal(), 'object', 3, 'security', 2, self::NUKE, null, null);

        $result = $this->resolver->resolve(array($ace1, $ace2, $ace3), 3);

        $this->assertEquals(self::DELETE | self::NUKE, $result['effectiveMask']);
        $this->assertEquals(self::NUKE, $result['mask']);
        $this->assertNull($result['stopMask']);
        $this->assertNull($result['noInheritMask']);
        $this->assertEquals(self::WRITE | self::DELETE, $result['parentMask']);
        $this->assertNull($result['parentStopMask']);
        $this->assertEquals(self::WRITE, $result['parentNoInheritMask']);
    }

    public function testThreeLevelsWithStopMaskAndNoInheritMask()
    {
        $acl = $this->prophesize(AccessControlList::class);

        $ace1 = new Entry($acl->reveal(), 'object', 1, 'security', 2, self::READ | self::WRITE | self::DELETE, null, self::WRITE);
        $ace2 = new Entry($acl->reveal(), 'object', 2, 'security', 2, self::NUKE, self::READ, self::NUKE);
        $ace3 = new Entry($acl->reveal(), 'object', 3, 'security', 2, self::ADMIN, null, null);

        $result = $this->resolver->resolve(array($ace1, $ace2, $ace3), 3);

        $this->assertEquals(self::DELETE | self::ADMIN, $result['effectiveMask']);
        $this->assertEquals(self::ADMIN, $result['mask']);
        $this->assertNull($result['stopMask']);
        $this->assertNull($result['noInheritMask']);
        $this->assertEquals(self::DELETE | self::NUKE, $result['parentMask']);
        $this->assertEquals(self::READ, $result['parentStopMask']);
        $this->assertEquals(self::NUKE, $result['parentNoInheritMask']);
    }
}
