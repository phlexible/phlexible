<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Tests\Permission;

use Phlexible\Bundle\AccessControlBundle\Entity\AccessControlEntry;

class HierarchyMaskResolver
{
    /**
     * @param AccessControlEntry[] $path
     *
     * @return int
     */
    public function resolve(array $path)
    {
        $first = true;
        $mask = 0;

        while (count($path)) {
            $ace = array_shift($path);

            $currentMask = $ace->getMask();
            if (!$first && $ace->getStopMask()) {
                // apply stop mask
                $currentMask = $currentMask ^ $ace->getStopMask();
            }
            if (count($path) && $ace->getNoInheritMask()) {
                // apply no inherit mask
                $currentMask = $currentMask ^ $ace->getNoInheritMask();
            }
            $mask = $currentMask ^ $mask;
            $first = false;
        }

        return $mask;
    }
}

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
        $ace = new AccessControlEntry();
        $ace
            ->setMask(1 | 2);

        $mask = $this->resolver->resolve(array($ace));

        $this->assertEquals(1 | 2, $mask);
    }

    public function testTwoLevels()
    {
        $ace1 = new AccessControlEntry();
        $ace1
            ->setMask(1 | 2);

        $ace2 = new AccessControlEntry();
        $ace2
            ->setMask(4);

        $mask = $this->resolver->resolve(array($ace1, $ace2));

        $this->assertEquals(1 | 2 | 4, $mask);
    }

    public function testTwoLevelsStopAll()
    {
        $ace1 = new AccessControlEntry();
        $ace1
            ->setMask(1 | 2);

        $ace2 = new AccessControlEntry();
        $ace2
            ->setStopMask(1 | 2);

        $mask = $this->resolver->resolve(array($ace1, $ace2));

        $this->assertEquals(0, $mask);
    }

    public function testTwoLevelsWithStopMask()
    {
        $ace1 = new AccessControlEntry();
        $ace1
            ->setMask(1 | 2);

        $ace2 = new AccessControlEntry();
        $ace2
            ->setMask(4)
            ->setStopMask(1);

        $mask = $this->resolver->resolve(array($ace1, $ace2));

        $this->assertEquals(2 | 4, $mask);
    }

    public function testTwoLevelsWithNoInheritMask()
    {
        $ace1 = new AccessControlEntry();
        $ace1
            ->setMask(1 | 2)
            ->setNoInheritMask(2);

        $ace2 = new AccessControlEntry();
        $ace2
            ->setMask(4);

        $mask = $this->resolver->resolve(array($ace1, $ace2));

        $this->assertEquals(1 | 4, $mask);
    }

    public function testTwoLevelsWithStopMaskAndNoInheritMask()
    {
        $ace1 = new AccessControlEntry();
        $ace1
            ->setMask(1 | 2 | 4)
            ->setNoInheritMask(2);

        $ace2 = new AccessControlEntry();
        $ace2
            ->setStopMask(1)
            ->setMask(8);

        $mask = $this->resolver->resolve(array($ace1, $ace2));

        $this->assertEquals(4 | 8, $mask);
    }

    public function testThreeLevels()
    {
        $ace1 = new AccessControlEntry();
        $ace1
            ->setMask(1 | 2);

        $ace2 = new AccessControlEntry();
        $ace2
            ->setMask(4);

        $ace3 = new AccessControlEntry();
        $ace3
            ->setMask(8);

        $mask = $this->resolver->resolve(array($ace1, $ace2, $ace3));

        $this->assertEquals(1 | 2 | 4 | 8, $mask);
    }

    public function testThreeLevelsStopAll()
    {
        $ace1 = new AccessControlEntry();
        $ace1
            ->setMask(1 | 2);

        $ace2 = new AccessControlEntry();
        $ace2
            ->setMask(4);

        $ace3 = new AccessControlEntry();
        $ace3
            ->setStopMask(1 | 2 | 4);

        $mask = $this->resolver->resolve(array($ace1, $ace2, $ace3));

        $this->assertEquals(0, $mask);
    }

    public function testThreeLevelsWithStopMask()
    {
        $ace1 = new AccessControlEntry();
        $ace1
            ->setMask(1 | 2);

        $ace2 = new AccessControlEntry();
        $ace2
            ->setMask(4)
            ->setStopMask(1);

        $ace3 = new AccessControlEntry();
        $ace3
            ->setMask(8)
            ->setStopMask(4);

        $mask = $this->resolver->resolve(array($ace1, $ace2, $ace3));

        $this->assertEquals(2 | 8, $mask);
    }

    public function testThreeLevelsWithNoInheritMask()
    {
        $ace1 = new AccessControlEntry();
        $ace1
            ->setMask(1 | 2)
            ->setNoInheritMask(2);

        $ace2 = new AccessControlEntry();
        $ace2
            ->setMask(4)
            ->setNoInheritMask(1);

        $ace3 = new AccessControlEntry();
        $ace3
            ->setMask(8);

        $mask = $this->resolver->resolve(array($ace1, $ace2, $ace3));

        $this->assertEquals(4 | 8, $mask);
    }

    public function testThreeLevelsWithStopMaskAndNoInheritMask()
    {
        $ace1 = new AccessControlEntry();
        $ace1
            ->setMask(1 | 2 | 4)
            ->setNoInheritMask(2);

        $ace2 = new AccessControlEntry();
        $ace2
            ->setMask(8 | 16)
            ->setStopMask(1)
            ->setNoInheritMask(8);

        $ace3 = new AccessControlEntry();
        $ace3
            ->setMask(32);

        $mask = $this->resolver->resolve(array($ace1, $ace2, $ace3));

        $this->assertEquals(4 | 16 | 32, $mask);
    }
}
