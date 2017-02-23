<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\Compressor;

use Phlexible\Bundle\GuiBundle\Routing\RouteExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * Simple css compressor test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RouteExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testExtract()
    {
        $router = $this->prophesize('Symfony\Component\Routing\RouterInterface');
        $router->getRouteCollection()->willReturn(array(
            new Route('/foo'),
            new Route('/bar', array('test' => 123)),
            new Route('/baz/{id}', array('id' => 234)),
        ));

        $request = new Request();

        $extractor = new RouteExtractor($router->reveal());
        $data = $extractor->extract($request);

        $this->assertSame('', $data->getBasePath());
        $this->assertSame('', $data->getBaseUrl());
        $this->assertCount(3, $data->getRoutes());
        $this->assertSame(array('path' => '/foo', 'variables' => array(), 'defaults' => array()), $data->getRoutes()[0]);
        $this->assertSame(array('path' => '/bar', 'variables' => array(), 'defaults' => array('test' => 123)), $data->getRoutes()[1]);
        $this->assertSame(array('path' => '/baz/{id}', 'variables' => array('id'), 'defaults' => array('id' => 234)), $data->getRoutes()[2]);
    }
}
