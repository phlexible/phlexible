<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Util\Tests;

use Phlexible\Component\Util\ArrayUtil;
use PHPUnit\Framework\TestCase;

/**
 * Array util Test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @covers \Phlexible\Component\Util\ArrayUtil
 */
class ArrayUtilTest extends TestCase
{
    /**
     * @var ArrayUtil
     */
    private $util;

    public function setUp()
    {
        $this->util = new ArrayUtil();
    }

    public function testColumnEmpty()
    {
        $src = [];

        $result = $this->util->column($src, 'a');

        $expected = [];

        $this->assertEquals($expected, $result);
    }

    public function testColumnNormal()
    {
        $src = [
            ['a1', 'b1'],
            ['a2', 'b2'],
            ['a3', 'b3'],
        ];

        $result = $this->util->column($src, 0);

        $expected = ['a1', 'a2', 'a3'];

        $this->assertEquals($expected, $result);
    }

    public function testColumnAssoziative()
    {
        $src = [
            ['d' => 'a1', 'e' => 'b1'],
            ['d' => 'a2', 'e' => 'b2'],
            ['d' => 'a3', 'e' => 'b3'],
        ];

        $result = $this->util->column($src, 'd');

        $expected = ['a1', 'a2', 'a3'];

        $this->assertEquals($expected, $result);
    }

    public function testColumnAssoziativeMissingKey()
    {
        $src = [
            ['d' => 'a1', 'e' => 'b1'],
            ['x' => 'a2', 'e' => 'b2'],
            ['d' => 'a3', 'e' => 'b3'],
        ];

        $result = $this->util->column($src, 'd');

        $expected = ['a1', null, 'a3'];

        $this->assertEquals($expected, $result);
    }

    public function testColumnAssoziativeMissingKeyWithSkip()
    {
        $src = [
            ['d' => 'a1', 'e' => 'b1'],
            ['x' => 'a2', 'e' => 'b2'],
            ['d' => 'a3', 'e' => 'b3'],
        ];

        $result = $this->util->column($src, 'd', true);

        $expected = [0 => 'a1', 2 => 'a3'];

        $this->assertEquals($expected, $result);
    }

    public function testColumnAssoziativeMissingKeyWithSkipEmpty()
    {
        $src = [
            ['d' => '', 'e' => 'b1'],
            ['x' => 'a2', 'e' => 'b2'],
            ['d' => 'a3', 'e' => 'b3'],
        ];

        $result = $this->util->column($src, 'd', true, true);

        $expected = [2 => 'a3'];

        $this->assertEquals($expected, $result);
    }

    public function testGet()
    {
        $src = ['eins', 'zwei', 5 => 'fünf', 'abc' => 'def'];

        $this->assertEquals('eins', $this->util->get($src, 0));
        $this->assertEquals('zwei', $this->util->get($src, 1));
        $this->assertEquals('fünf', $this->util->get($src, 5));
        $this->assertEquals('fünf', $this->util->get($src, 5, 'default'));
        $this->assertEquals('def', $this->util->get($src, 'abc'));
        $this->assertEquals('def', $this->util->get($src, 'abc', 'default'));
        $this->assertEquals('default', $this->util->get($src, 'xxx', 'default'));

        $this->assertNull($this->util->get($src, 'xxx'));
    }

    public function testGroupByEmpty()
    {
        $src = [];

        $result = $this->util->groupBy($src, 'col');

        $expected = [];

        $this->assertEquals($expected, $result);
    }

    public function testGroupByOneColumn()
    {
        $src = [
            ['key' => 1, 'value' => '1'],
            ['key' => 3, 'value' => '1'],
            ['key' => 2, 'value' => '1'],
            ['key' => 3, 'value' => '2'],
            ['key' => 3, 'value' => '3'],
            ['key' => 2, 'value' => '2'],
        ];

        $result = $this->util->groupBy($src, 'key');

        $expected = [
            '1' => [
                ['key' => 1, 'value' => '1'],
            ],
            '3' => [
                ['key' => 3, 'value' => '1'],
                ['key' => 3, 'value' => '2'],
                ['key' => 3, 'value' => '3'],
            ],
            '2' => [
                ['key' => 2, 'value' => '1'],
                ['key' => 2, 'value' => '2'],
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGroupByTwoColumns()
    {
        $src = [
            ['keyA' => 1, 'keyB' => 1, 'value' => '1'],
            ['keyA' => 3, 'keyB' => 1, 'value' => '2'],
            ['keyA' => 2, 'keyB' => 1, 'value' => '1'],
            ['keyA' => 1, 'keyB' => 1, 'value' => '2'],
            ['keyA' => 2, 'keyB' => 1, 'value' => '2'],
            ['keyA' => 1, 'keyB' => 2, 'value' => '1'],
            ['keyA' => 2, 'keyB' => 2, 'value' => '1'],
            ['keyA' => 2, 'keyB' => 2, 'value' => '2'],
            ['keyA' => 3, 'keyB' => 1, 'value' => '1'],
            ['keyA' => 2, 'keyB' => 3, 'value' => '1'],
            ['keyA' => 3, 'keyB' => 1, 'value' => '3'],
        ];

        $result = $this->util->groupBy($src, ['keyA', 'keyB']);

        $expected = [
            '1' => [
                '1' => [
                    ['keyA' => 1, 'keyB' => 1, 'value' => '1'],
                    ['keyA' => 1, 'keyB' => 1, 'value' => '2'],
                ],
                '2' => [
                    ['keyA' => 1, 'keyB' => 2, 'value' => '1'],
                ],
            ],
            '3' => [
                '1' => [
                    ['keyA' => 3, 'keyB' => 1, 'value' => '2'],
                    ['keyA' => 3, 'keyB' => 1, 'value' => '1'],
                    ['keyA' => 3, 'keyB' => 1, 'value' => '3'],
                ],
            ],
            '2' => [
                '1' => [
                    ['keyA' => 2, 'keyB' => 1, 'value' => '1'],
                    ['keyA' => 2, 'keyB' => 1, 'value' => '2'],
                ],
                '2' => [
                    ['keyA' => 2, 'keyB' => 2, 'value' => '1'],
                    ['keyA' => 2, 'keyB' => 2, 'value' => '2'],
                ],
                '3' => [
                    ['keyA' => 2, 'keyB' => 3, 'value' => '1'],
                ],
            ],
        ];

        $this->assertEquals($expected, $result);
    }
}
