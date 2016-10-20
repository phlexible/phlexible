<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Route extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RouteExtractor implements RouteExtractorInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function extract(Request $request)
    {
        $baseUrl = $request->getBaseUrl();
        $basePath = $request->getBasePath();

        $routes = [];
        foreach ($this->router->getRouteCollection() as $id => $route) {
            /* @var $route Route */

            $compiledRoute = $route->compile();
            $variables = $compiledRoute->getVariables();
            $routeDefaults = $route->getDefaults();
            $defaults = [];
            foreach ($routeDefaults as $key => $default) {
                $defaults[$key] = $default;
            }

            try {
                $routes[$id] = [
                    'path'      => $baseUrl . $route->getPath(),
                    'variables' => array_values($variables),
                    'defaults'  => $defaults,
                ];
            } catch (\Exception $e) {
            }
        }

        return new ExtractedRoutes($baseUrl, $basePath, $routes);
    }
}
