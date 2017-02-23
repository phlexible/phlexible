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

use Phlexible\Component\Util\StringUtil;

/**
 * String util Test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class StringUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StringUtil
     */
    private $util;

    public function setUp()
    {
        $this->util = new StringUtil();
    }

    public function testTruncatePreservingTags()
    {
        $test = '<div class="testomat">p<p style="color:#123456">o</p>mmes</div>';

        $this->assertEquals($this->util->truncatePreservingTags($test, 1), '<div class="testomat">p...</div>');
        $this->assertEquals($this->util->truncatePreservingTags($test, 2), '<div class="testomat">p<p style="color:#123456">o...</p></div>');
        $this->assertEquals($this->util->truncatePreservingTags($test, 5), '<div class="testomat">p<p style="color:#123456">o</p>mme...</div>');
    }

    public function testTruncatePreservingTagsWithPostString()
    {
        $test = '<div class="testomat">p<p style="color:#123456">o</p>mmes</div>';

        $this->assertEquals($this->util->truncatePreservingTags($test, 1, '####'), '<div class="testomat">p####</div>');
        $this->assertEquals($this->util->truncatePreservingTags($test, 2, '####'), '<div class="testomat">p<p style="color:#123456">o####</p></div>');
        $this->assertEquals($this->util->truncatePreservingTags($test, 5, '####'), '<div class="testomat">p<p style="color:#123456">o</p>mme####</div>');
    }

    /**
     * truncate string preserving tags.
     */
    public function testTruncatePreservingTagsWithMultibyteCharacters()
    {
        $str = 'äüö<strong>1</strong>23456789';
        $match = 'äüö<strong>1</strong>2345';

        $test = $this->util->truncatePreservingTags($str, 8, '');

        $this->assertEquals($test, $match,
            'string match failed'.PHP_EOL.'orig: '.$str.PHP_EOL.'return: '.$test.PHP_EOL.'match: '.$match);
    }

    /**
     * truncate string preserving tags.
     */
    public function testTruncatePreservingTagsWithGreaterThanBracket()
    {
        $str = '1>2345<strong>67</strong>890';
        $match = '1>2345<strong>67##</strong>';

        $test = $this->util->truncatePreservingTags($str, 8, '##');

        $this->assertEquals($test, $match,
            'string match failed'.PHP_EOL.'orig: '.$str.PHP_EOL.'return: '.$test.PHP_EOL.'match: '.$match);
    }

    /**
     * truncate string preserving tags.
     */
    public function testTruncatePreservingTagsWithLessThanBracket()
    {
        $str = '1<2345<strong>67</strong>890';
        $match = '1<2345<strong>67##</strong>';

        $test = $this->util->truncatePreservingTags($str, 8, '##');

        $this->assertEquals($test, $match,
            'string match failed'.PHP_EOL.'orig: '.$str.PHP_EOL.'return: '.$test.PHP_EOL.'match: '.$match);
    }

    /**
     * truncate string preserving tags.
     */
    public function testTruncatePreservingTagsWithLessAndBiggerThanBracket()
    {
        $str = '1<>2345<strong>67</strong>890';
        $match = '1<>2345<strong>6##</strong>';

        $test = $this->util->truncatePreservingTags($str, 8, '##');

        $this->assertEquals($test, $match,
            'string match failed'.PHP_EOL.'orig: '.$str.PHP_EOL.'return: '.$test.PHP_EOL.'match: '.$match);
    }

    /**
     * truncate string preserving tags.
     */
    public function testTruncatePreservingTagsWithUnclosedTag()
    {
        $str = '<test>12345<strong>67</strong>890';
        $match = '<test>12345<strong>67</strong>8##</test>';

        $test = $this->util->truncatePreservingTags($str, 8, '##');

        $this->assertEquals($test, $match,
            'string match failed'.PHP_EOL.'orig: '.$str.PHP_EOL.'return: '.$test.PHP_EOL.'match: '.$match);
    }

    /**
     * truncate string preserving tags.
     */
    public function testTruncatePreservingTagsWithLongText()
    {
        $str = '12345<strong>67</strong>890';
        $str .= '12345<strong>67</strong>890';
        $str .= '12345<strong>67</strong>890';
        $str .= '12345<strong>67</strong>890';
        $str .= '12345<strong>67</strong>890';
        $str .= '12345<strong>67</strong>890';

        $match = '12345<strong>67</strong>890';
        $match .= '12345<strong>67</strong>890';
        $match .= '12345<strong>67</strong>890';
        $match .= '12345<strong>67</strong>89##';

        $test = $this->util->truncatePreservingTags($str, 39, '##');

        $this->assertEquals($test, $match,
            'string match failed'.PHP_EOL.'orig: '.$str.PHP_EOL.'return: '.$test.PHP_EOL.'match: '.$match);
    }
}
