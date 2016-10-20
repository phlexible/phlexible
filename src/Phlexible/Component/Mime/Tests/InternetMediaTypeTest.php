<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Mime\Tests;

use Phlexible\Component\Mime\InternetMediaType;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class InternetMediaTypeTest extends TestCase
{
    public function testHasTypeReturnsFalseOnEmptyType()
    {
        $internetMediaType = new InternetMediaType();

        $this->assertFalse($internetMediaType->hasType());
    }

    public function testHasSubtypeReturnsFalseOnEmptySubtype()
    {
        $internetMediaType = new InternetMediaType();

        $this->assertFalse($internetMediaType->hasSubtype());
    }

    public function testHasParametersReturnsFalseOnEmptyParameters()
    {
        $internetMediaType = new InternetMediaType();

        $this->assertFalse($internetMediaType->hasParameters());
    }

    public function testHasTypeWithConstructorInjectionReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType('image');

        $this->assertTrue($internetMediaType->hasType());
    }

    public function testHasSubtypeWithConstructorInjectionReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType('image', 'jpeg');

        $this->assertTrue($internetMediaType->hasSubtype());
    }

    public function testHasParametersWithConstructorInjectionReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType('image', 'jpeg', ['charset' => 'utf8']);

        $this->assertTrue($internetMediaType->hasParameters());
    }

    public function testHasTypeWithSetTypeReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType();
        $internetMediaType->setType('image');

        $this->assertTrue($internetMediaType->hasType());
    }

    public function testHasSubtypeWithSetSubtypeReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType();
        $internetMediaType->setSubtype('jpeg');

        $this->assertTrue($internetMediaType->hasSubtype());
    }

    public function testHasParametersWithSetParametersReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType();
        $internetMediaType->setParameters(['charset' => 'utf8']);

        $this->assertTrue($internetMediaType->hasParameters());
    }

    public function testGetTypeWithConstructorInjectionReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType('image');

        $this->assertSame('image', $internetMediaType->getType());
    }

    public function testGetSubtypeWithConstructorInjectionReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType('image', 'jpeg');

        $this->assertSame('jpeg', $internetMediaType->getSubtype());
    }

    public function testGetParametersWithConstructorInjectionReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType('image', 'jpeg', ['charset' => 'utf8']);

        $this->assertSame(['charset' => 'utf8'], $internetMediaType->getParameters());
    }

    public function testGetTypeWithSetTypeReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType();
        $internetMediaType->setType('image');

        $this->assertSame('image', $internetMediaType->getType());
    }

    public function testGetSubtypeWithSetSubtypeReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType();
        $internetMediaType->setSubtype('jpeg');

        $this->assertSame('jpeg', $internetMediaType->getSubtype());
    }

    public function testGetParametersWithSetParametersReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType();
        $internetMediaType->setParameters(['charset' => 'utf8']);

        $this->assertSame(['charset' => 'utf8'], $internetMediaType->getParameters());
    }

    public function testSetTypeOverwritesConstructorInjectedType()
    {
        $internetMediaType = new InternetMediaType('image');
        $internetMediaType->setType('document');

        $this->assertSame('document', $internetMediaType->getType());
    }

    public function testSetSubtypeOverwritesConstructorInjectedSubtype()
    {
        $internetMediaType = new InternetMediaType('image', 'jpeg');
        $internetMediaType->setSubtype('png');

        $this->assertSame('png', $internetMediaType->getSubtype());
    }

    public function testSetParametersOverwritesConstructorInjectedParameters()
    {
        $internetMediaType = new InternetMediaType('image', 'jpg', ['charset' => 'utf8']);
        $internetMediaType->setParameters(['key' => 'value']);

        $this->assertSame(['key' => 'value'], $internetMediaType->getParameters());
    }

    public function testToStringWithoutParametersReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType('image', 'jpeg', ['charset' => 'utf8']);

        $this->assertSame('image/jpeg', $internetMediaType->toStringWithoutParameters());
    }

    public function testToStringReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType('image', 'jpeg', ['charset' => 'utf8']);

        $this->assertSame('image/jpeg; charset=utf8', $internetMediaType->toString());
    }

    public function testMagicToStringReturnsCorrectValue()
    {
        $internetMediaType = new InternetMediaType('image', 'jpeg', ['charset' => 'utf8']);

        $this->assertSame('image/jpeg; charset=utf8', (string) $internetMediaType);
    }
}
