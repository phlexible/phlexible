<?php

namespace Phlexible\Component\Database\Tests\Functions;

use Phlexible\Component\Database\Functions\MysqlFunctions;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Mysql functions test case.
 */
class MysqlFunctionsTest extends TestCase
{
    public function setUp()
    {
        $this->fn = new MysqlFunctions();
    }

    public function testExprReturnsExpression()
    {
        $this->assertInstanceOf('Zend_Db_Expr', $this->fn->expr('test'));
    }

    public function testExprHasCorrectValue()
    {
        $this->assertSame('test', (string)$this->fn->expr('test'));
    }

    public function testNowReturnsExpression()
    {
        $this->assertInstanceOf('Zend_Db_Expr', $this->fn->now());
    }

    public function testNowHasCorrectValue()
    {
        $this->assertSame('NOW()', (string)$this->fn->now());
    }

    public function testConcatReturnsExpression()
    {
        $this->assertInstanceOf('Zend_Db_Expr', $this->fn->concat('test'));
    }

    public function testConcatHasCorrectValue()
    {
        $this->assertSame("CONCAT('A')", (string)$this->fn->concat('A'));
    }

    public function testConcatHasCorrectValueWithFunctionArguments()
    {
        $this->assertSame("CONCAT('A','B','C')", (string)$this->fn->concat('A', 'B', 'C'));
    }

    public function testConcatHasCorrectValueWithFunctionArgumentsAndExpression()
    {
        $this->assertSame("CONCAT('A','B','C',D)", (string)$this->fn->concat('A', 'B', 'C', new \Zend_Db_Expr('D')));
    }

    public function testDateAddReturnsExpression()
    {
        $this->assertInstanceOf('Zend_Db_Expr', $this->fn->dateAdd('2011-11-11', 'year', '1'));
    }

    public function testDateAddHasCorrectValue()
    {
        $this->assertSame("DATE_ADD('2011-11-11', INTERVAL 1 YEAR)", (string)$this->fn->dateAdd('2011-11-11', 'year', '1'));
    }

    public function testDateAddHasNowDateWithEmptyDate()
    {
        $this->assertSame('DATE_ADD(NOW(), INTERVAL 2 MONTH)', (string)$this->fn->dateAdd(null, 'month', '2'));
    }

    public function testDateSubReturnsExpression()
    {
        $this->assertInstanceOf('Zend_Db_Expr', $this->fn->dateSub('2011-11-11', 'year', '1'));
    }

    public function testDateSubHasCorrectValue()
    {
        $this->assertSame("DATE_SUB('2011-11-11', INTERVAL 1 YEAR)", (string)$this->fn->dateSub('2011-11-11', 'year', '1'));
    }

    public function testDateSubHasNowDateWithEmptyDate()
    {
        $this->assertSame('DATE_SUB(NOW(), INTERVAL 2 MONTH)', (string)$this->fn->dateSub(null, 'month', '2'));
    }

    public function testUnixtimeReturnsExpression()
    {
        $this->assertInstanceOf('Zend_Db_Expr', $this->fn->unixtime());
    }

    public function testUnixtimeHasCorrectValueWithNoArgument()
    {
        $this->assertSame("UNIX_TIMESTAMP()", (string)$this->fn->unixtime());
    }

    public function testUnixtimeHasCorrectValueWithExpressionArgument()
    {
        $this->assertSame("UNIX_TIMESTAMP(col)", (string)$this->fn->unixtime(new \Zend_Db_Expr('col')));
    }

    public function testUnixtimeHasCorrectValueWithStringArgument()
    {
        $this->assertSame("UNIX_TIMESTAMP('2011-11-11')", (string)$this->fn->unixtime('2011-11-11'));
    }
    public function testStrPosReturnsExpression()
    {
        $this->assertInstanceOf('Zend_Db_Expr', $this->fn->strPos('a', 'b'));
    }

    public function testStrPosHasCorrectValueWithExpression()
    {
        $this->assertSame("INSTR(col, 'b')", (string)$this->fn->strPos(new \Zend_Db_Expr('col'), 'b'));
    }

    public function testStrPosHasCorrectValueWithStrings()
    {
        $this->assertSame("INSTR('test', 'es')", (string)$this->fn->strPos('test', 'es'));
    }
}
