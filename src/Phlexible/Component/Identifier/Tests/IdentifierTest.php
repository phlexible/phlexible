<?php

namespace Phlexible\Component\Identifier\Tests;

use Phlexible\Component\Identifier\Identifier;
use PHPUnit_Framework_TestCase as TestCase;

class IdentifierTest extends TestCase
{
    /**
     * @expectedException \Phlexible\Component\Identifier\Exception\InvalidArgumentException
     */
    public function testInstanciateWithoutArguments()
    {
        new Identifier();
    }

    public function testInstanciateWithOneArgument()
    {
        $identifier = new Identifier('name');

        $this->assertSame(str_replace('\\', '_', get_class($identifier)) . '__name', (string) $identifier);
    }

    public function testInstanciateWithTwoArguments()
    {
        $identifier = new Identifier('id', 3);

        $this->assertSame(str_replace('\\', '_', get_class($identifier)) . '__id__3', (string) $identifier);
    }
}
