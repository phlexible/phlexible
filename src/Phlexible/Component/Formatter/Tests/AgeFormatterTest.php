<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Formatter\Tests;

use Phlexible\Component\Formatter\AgeFormatter;

/**
 * Age formatter tests
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AgeFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AgeFormatter
     */
    private $formatter;

    public function setUp()
    {
        $this->formatter = new AgeFormatter();
    }

    public function testFormatDateWithStringResult()
    {
        $this->assertEquals('1 second', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 00:00:01'));
        $this->assertEquals('7 seconds', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 00:00:07'));
        $this->assertEquals('31 seconds', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 00:00:31'));
        $this->assertEquals('1 minute', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 00:01:00'));
        $this->assertEquals('6 minutes', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 00:06:00'));
        $this->assertEquals('48 minutes', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 00:48:00'));
        $this->assertEquals('1 hour', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 01:00:00'));
        $this->assertEquals('5 hours', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 05:00:00'));
        $this->assertEquals('22 hours', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 22:00:00'));
        $this->assertEquals('1 day', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-02 00:00:00'));
        $this->assertEquals('2 days', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-03 00:00:00'));
        $this->assertEquals('6 days', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-07 00:00:00'));
        $this->assertEquals('1 week', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-10 00:00:00'));
        $this->assertEquals('2 weeks', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-16 00:00:00'));
        $this->assertEquals('3 weeks', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-22 00:00:00'));
        $this->assertEquals('1 month', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-02-01 00:00:00'));
        $this->assertEquals('2 months', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-03-01 00:00:00'));
        $this->assertEquals('10 months', $this->formatter->formatDate('2010-01-01 00:00:00', '2010-11-01 00:00:00'));
        $this->assertEquals('1 year', $this->formatter->formatDate('2010-01-01 00:00:00', '2011-01-01 00:00:00'));
        $this->assertEquals('5 years', $this->formatter->formatDate('2010-01-01 00:00:00', '2015-01-01 00:00:00'));
        $this->assertEquals('18 years', $this->formatter->formatDate('2010-01-01 00:00:00', '2028-01-01 00:00:00'));
    }

    public function testFormatDateWithArrayResult()
    {
        $this->assertEquals(['1',  'second'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 00:00:01', true));
        $this->assertEquals(['7', 'seconds'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 00:00:07', true));
        $this->assertEquals(['31', 'seconds'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 00:00:31', true));
        $this->assertEquals(['1', 'minute'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 00:01:00', true));
        $this->assertEquals(['6', 'minutes'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 00:06:00', true));
        $this->assertEquals(['48', 'minutes'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 00:48:00', true));
        $this->assertEquals(['1', 'hour'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 01:00:00', true));
        $this->assertEquals(['5', 'hours'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 05:00:00', true));
        $this->assertEquals(['22', 'hours'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-01 22:00:00', true));
        $this->assertEquals(['1', 'day'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-02 00:00:00', true));
        $this->assertEquals(['2', 'days'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-03 00:00:00', true));
        $this->assertEquals(['6', 'days'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-07 00:00:00', true));
        $this->assertEquals(['1', 'week'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-10 00:00:00', true));
        $this->assertEquals(['2', 'weeks'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-16 00:00:00', true));
        $this->assertEquals(['3', 'weeks'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-01-22 00:00:00', true));
        $this->assertEquals(['1', 'month'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-02-01 00:00:00', true));
        $this->assertEquals(['2', 'months'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-03-01 00:00:00', true));
        $this->assertEquals(['10', 'months'], $this->formatter->formatDate('2010-01-01 00:00:00', '2010-11-01 00:00:00', true));
        $this->assertEquals(['1', 'year'], $this->formatter->formatDate('2010-01-01 00:00:00', '2011-01-01 00:00:00', true));
        $this->assertEquals(['5', 'years'], $this->formatter->formatDate('2010-01-01 00:00:00', '2015-01-01 00:00:00', true));
        $this->assertEquals(['18', 'years'], $this->formatter->formatDate('2010-01-01 00:00:00', '2028-01-01 00:00:00', true));
    }
}
