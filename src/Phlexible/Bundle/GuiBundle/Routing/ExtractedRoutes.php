<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Extracted routes
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExtractedRoutes
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var array
     */
    private $routes;

    /**
     * @param string $baseUrl
     * @param string $basePath
     * @param array  $routes
     */
    public function __construct($baseUrl, $basePath, array $routes)
    {
        $this->baseUrl = $baseUrl;
        $this->basePath = $basePath;
        $this->routes = $routes;
    }

    /**
     * Return base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Return base path
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Return routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
