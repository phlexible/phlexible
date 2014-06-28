<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Tests\Util;

use Phlexible\Bundle\ElementBundle\Util\SuggestFieldUtil;

/**
 * Elements events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SuggestFieldUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Call constructor with DbPool
     */
    public function testCallConstructorWithDbPool()
    {
        // EXERCISE
        $suggestFieldUtil = new SuggestFieldUtil($dbPool, '#');

        // VERIFY
        $this->assertTrue(
            $suggestFieldUtil instanceof SuggestFieldUtil,
            'constructor failed'
        );
    }

    /**
     * splitSuggestValues() splits string
     */
    public function testSplitSuggestValuesSplitsStrings()
    {
        // SETUP
        $splittedOriginal1 = array('a', 'b', 'cde');
        $splittedOriginal2 = array('f', 'g', 'hi');

        $concatenatedValues = array(
            implode('#', $splittedOriginal1),
            implode('#', $splittedOriginal2),
        );

        $suggestFieldUtil = new SuggestFieldUtil($dbPool, '#');

        // EXERCISE
        $splitted = $suggestFieldUtil->splitSuggestValues($concatenatedValues);

        // VERIFY
        $this->assertEquals(array_merge($splittedOriginal1, $splittedOriginal2), $splitted, 'values not splitted correctly');
    }

    /**
     * splitSuggestValues() removes empty values
     */
    public function testSplitSuggestValuesRemovesEmptyValues()
    {
        // SETUP
        $splittedOriginal = array('a', 'b', ' ', '');
        $expected         = array('a', 'b');

        $concatenatedValues = array(
            implode('#', $splittedOriginal),
        );

        $suggestFieldUtil = new SuggestFieldUtil($dbPool, '#');

        // EXERCISE
        $splitted = $suggestFieldUtil->splitSuggestValues($concatenatedValues);

        // VERIFY
        $this->assertEquals(
            array_values($expected),
            array_values($splitted),
            'values not splitted correctly'
        );
    }

    /**
     * splitSuggestValues() removes duplicate values
     */
    public function testSplitSuggestValuesRemovesDuplicateValues()
    {
        // SETUP
        $concatenatedValues = $this->helperConcatenate(
            array(
                array('a', 'b', 'cde'),
                array('a', 'hi', 'b'),
            )
        );

        $expected = array('a', 'b', 'cde', 'hi');

        $suggestFieldUtil = new SuggestFieldUtil($dbPool, '#');

        // EXERCISE
        $splitted = $suggestFieldUtil->splitSuggestValues($concatenatedValues);

        // VERIFY
        $this->assertEquals(
            array_values($expected),
            array_values($splitted),
            'values not splitted correctly'
        );
    }

    /**
     * fetchOnlineValues() fetches data from table, splits and remove duplicates
     */
    public function testFetchOnlineValuesFetchesDataFromTableSplitsAndRemoveDuplicates()
    {
        // SETUP
        $dsid = 'dsid';

        $concatenatedValues = $this->helperConcatenate(
            array(
                array('a', 'b', 'cde'),
                array('a', 'hi', 'b')
            )
        );

        $expected = array('a', 'b', 'cde', 'hi');

        $suggestFieldUtil = new SuggestFieldUtil($dbPool, '#');

        /* @var $adapter Zend_Test_DbAdapter */
        $adapter = $dbPool->default;
        $adapter->appendStatementToStack(
            Zend_Test_DbStatement::createSelectStatement($concatenatedValues)
        );

        // EXERCISE
        $splitted = $suggestFieldUtil->fetchOnlineValues($dsid, 'en');

        // VERIFY
        $this->assertEquals(
            array_values($expected),
            array_values($splitted),
            'duplicate values not removed'
        );
    }

    /**
     * fetchOnlineValues() fetches data from table, splits and remove duplicates
     */
    public function testFetchUsedValuesFetchesDataFromTableSplitsAndRemoveDuplicates()
    {
        // SETUP
        $dsid = 'dsid';

        $concatenatedValues = $this->helperConcatenate(
            array(
                array('a', 'b', 'cde'),
                array('a', 'hi', 'b')
            )
        );

        $expected = array('a', 'b', 'cde', 'hi');

        $suggestFieldUtil = new SuggestFieldUtil($dbPool, '#');

        /* @var $adapter Zend_Test_DbAdapter */
        $adapter = $dbPool->read;
        $adapter->appendStatementToStack(
            Zend_Test_DbStatement::createSelectStatement($concatenatedValues)
        );

        // EXERCISE
        $splitted = $suggestFieldUtil->fetchUsedValues($dsid, 'en');

        // VERIFY
        $this->assertEquals(
            array_values($expected),
            array_values($splitted)
        );
    }

    /**
     * Concatenate lines of splitted values.
     *
     * @param $splittedLines
     *
     * @return array of concatenated value lines
     */
    public function helperConcatenate(array $splittedLines)
    {
        $concatenatedLines = array();

        foreach ($splittedLines as $splittedLine) {
            $concatenatedLines[] = implode(
                '#',
                $splittedLine
            );
        }

        return $concatenatedLines;
    }
}
