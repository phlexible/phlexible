<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Tests\Permission;

use Phlexible\Bundle\AccessControlBundle\Entity\AccessControlEntry;
use Phlexible\Component\AccessControl\Domain\Entry;
use Phlexible\Component\AccessControl\Permission\HierarchyMaskResolver;

/**
 * Hierarchy mask resolver test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class HierarchyMaskResolverTest extends \PHPUnit_Framework_TestCase
{
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
        $ace = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace->getMask()->willReturn(1 | 2);

        $mask = $this->resolver->resolve(array($ace->reveal()));

        $this->assertEquals(1 | 2, $mask);
    }

    public function testTwoLevels()
    {
        $ace1 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace1->getMask()->willReturn(1 | 2);
        $ace1->getNoInheritMask()->willReturn(0);
        $ace1->getStopMask()->willReturn(0);

        $ace2 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace2->getMask()->willReturn(4);
        $ace2->getNoInheritMask()->willReturn(0);
        $ace2->getStopMask()->willReturn(0);

        $mask = $this->resolver->resolve(array($ace1->reveal(), $ace2->reveal()));

        $this->assertEquals(1 | 2 | 4, $mask);
    }

    public function testTwoLevelsStopAll()
    {
        $ace1 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace1->getMask()->willReturn(1 | 2);
        $ace1->getNoInheritMask()->willReturn(0);
        $ace1->getStopMask()->willReturn(0);

        $ace2 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace2->getMask()->willReturn(1 | 2);
        $ace2->getNoInheritMask()->willReturn(0);
        $ace2->getStopMask()->willReturn(0);

        $mask = $this->resolver->resolve(array($ace1->reveal(), $ace2->reveal()));

        $this->assertEquals(0, $mask);
    }

    public function testTwoLevelsWithStopMask()
    {
        $ace1 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace1->getMask()->willReturn(1 | 2);
        $ace1->getNoInheritMask()->willReturn(0);
        $ace1->getStopMask()->willReturn(0);

        $ace2 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace2->getMask()->willReturn(4);
        $ace2->getNoInheritMask()->willReturn(0);
        $ace2->getStopMask()->willReturn(1);

        $mask = $this->resolver->resolve(array($ace1->reveal(), $ace2->reveal()));

        $this->assertEquals(2 | 4, $mask);
    }

    public function testTwoLevelsWithNoInheritMask()
    {
        $ace1 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace1->getMask()->willReturn(1 | 2);
        $ace1->getStopMask()->willReturn(0);
        $ace1->getNoInheritMask()->willReturn(2);

        $ace2 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace2->getMask()->willReturn(4);
        $ace2->getStopMask()->willReturn(0);
        $ace2->getNoInheritMask()->willReturn(0);

        $mask = $this->resolver->resolve(array($ace1->reveal(), $ace2->reveal()));

        $this->assertEquals(1 | 4, $mask);
    }

    public function testTwoLevelsWithStopMaskAndNoInheritMask()
    {
        $ace1 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace1->getMask()->willReturn(1 | 2 | 4);
        $ace1->getNoInheritMask()->willReturn(2);

        $ace2 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace2->getMask()->willReturn(8);
        $ace2->getStopMask()->willReturn(1);

        $mask = $this->resolver->resolve(array($ace1->reveal(), $ace2->reveal()));

        $this->assertEquals(4 | 8, $mask);
    }

    public function testThreeLevels()
    {
        $ace1 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace1->getMask()->willReturn(1 | 2);
        $ace1->getStopMask()->willReturn(0);
        $ace1->getNoInheritMask()->willReturn(0);

        $ace2 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace2->getMask()->willReturn(4);
        $ace2->getStopMask()->willReturn(0);
        $ace2->getNoInheritMask()->willReturn(0);

        $ace3 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace3->getMask()->willReturn(8);
        $ace3->getStopMask()->willReturn(0);
        $ace3->getNoInheritMask()->willReturn(0);

        $mask = $this->resolver->resolve(array($ace1->reveal(), $ace2->reveal(), $ace3->reveal()));

        $this->assertEquals(1 | 2 | 4 | 8, $mask);
    }

    public function testThreeLevelsStopAll()
    {
        $ace1 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace1->getMask()->willReturn(1 | 2);
        $ace1->getStopMask()->willReturn(0);
        $ace1->getNoInheritMask()->willReturn(0);

        $ace2 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace2->getMask()->willReturn(4);
        $ace2->getStopMask()->willReturn(0);
        $ace2->getNoInheritMask()->willReturn(0);

        $ace3 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace3->getMask()->willReturn(1 | 2 | 4);
        $ace3->getStopMask()->willReturn(0);
        $ace3->getNoInheritMask()->willReturn(0);

        $mask = $this->resolver->resolve(array($ace1->reveal(), $ace2->reveal(), $ace3->reveal()));

        $this->assertEquals(0, $mask);
    }

    public function testThreeLevelsWithStopMask()
    {
        $ace1 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace1->getMask()->willReturn(1 | 2);
        $ace1->getStopMask()->willReturn(0);
        $ace1->getNoInheritMask()->willReturn(0);

        $ace2 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace2->getMask()->willReturn(4);
        $ace2->getStopMask()->willReturn(1);
        $ace2->getNoInheritMask()->willReturn(0);

        $ace3 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace3->getMask()->willReturn(8);
        $ace3->getStopMask()->willReturn(4);
        $ace3->getNoInheritMask()->willReturn(0);

        $mask = $this->resolver->resolve(array($ace1->reveal(), $ace2->reveal(), $ace3->reveal()));

        $this->assertEquals(2 | 8, $mask);
    }

    public function testThreeLevelsWithNoInheritMask()
    {
        $ace1 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace1->getMask()->willReturn(1 | 2);
        $ace1->getNoInheritMask()->willReturn(2);
        $ace1->getStopMask()->willReturn(0);

        $ace2 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace2->getMask()->willReturn(4);
        $ace2->getNoInheritMask()->willReturn(1);
        $ace2->getStopMask()->willReturn(0);

        $ace3 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace3->getMask()->willReturn(8);
        $ace3->getStopMask()->willReturn(0);
        $ace3->getNoInheritMask()->willReturn(0);

        $mask = $this->resolver->resolve(array($ace1->reveal(), $ace2->reveal(), $ace3->reveal()));

        $this->assertEquals(4 | 8, $mask);
    }

    public function testThreeLevelsWithStopMaskAndNoInheritMask()
    {
        $ace1 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace1->getMask()->willReturn(1 | 2 | 4);
        $ace1->getNoInheritMask()->willReturn(2);
        $ace1->getStopMask()->willReturn(0);

        $ace2 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace2->getMask()->willReturn(8 | 16);
        $ace2->getStopMask()->willReturn(1);
        $ace2->getNoInheritMask()->willReturn(8);

        $ace3 = $this->prophesize('Phlexible\Component\AccessControl\Domain\Entry');
        $ace3->getMask()->willReturn(32);
        $ace3->getStopMask()->willReturn(0);
        $ace3->getNoInheritMask()->willReturn(0);

        $mask = $this->resolver->resolve(array($ace1->reveal(), $ace2->reveal(), $ace3->reveal()));

        $this->assertEquals(4 | 16 | 32, $mask);
    }
}
