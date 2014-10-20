<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route as RoutingRoute;

/**
 * Frame controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FrameController extends Controller
{
    /**
     * Render Frame
     *
     * @param Request $request
     *
     * @return array
     * @Route("", name="gui_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction(Request $request)
    {
        $viewIndex = $this->get('phlexible_gui.view.index');

        $securityContext = $this->get('security.context');

        return [
            'scripts'  => $viewIndex->get($request, $securityContext),
            'noScript' => $viewIndex->getNoScript(),
        ];
    }

    /**
     * Return configuration
     *
     * @return JsonResponse
     * @Route("/gui/config", name="gui_config")
     * @Method("GET")
     * @ApiDoc(
     *   description="Returns config values"
     * )
     */
    public function configAction()
    {
        $configBuilder = $this->get('phlexible_gui.builder.config');
        $config = $configBuilder->toArray();

        return new JsonResponse($config->getAll());
    }

    /**
     * Return menu
     *
     * @return JsonResponse
     * @Route("/gui/menu", name="gui_menu")
     * @Method("GET")
     * @ApiDoc(
     *   description="Returns menu structure"
     * )
     */
    public function menuAction()
    {
        $loader = $this->get('phlexible_gui.menu.loader');
        $items = $loader->load($this->container->getParameter('kernel.bundles'));
        $data = $items->toArray();

        return new JsonResponse($data);
    }

    /**
     * Return routes
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/gui/routes", name="gui_routes")
     * @Method("GET")
     * @ApiDoc(
     *   description="Returns routes"
     * )
     */
    public function routesAction(Request $request)
    {
        $router = $this->get('router');

        $routes = [];
        foreach ($router->getRouteCollection() as $id => $route) {
            /* @var $route RoutingRoute */

            $compiledRoute = $route->compile();
            $variables = $compiledRoute->getVariables();
            $placeholders = [];
            foreach ($variables as $variable) {
                $placeholders[$variable] = '{' . $variable . '}';
            }

            $routeDefaults = $route->getDefaults();
            $defaults = [];
            foreach ($routeDefaults as $key => $default) {
                if (!in_array($key, $variables)) {
                    continue;
                }
                $defaults[$key] = $default;
            }

            try {
                $routes[$id] = [
                    'path'      => $request->getBaseUrl() . $route->getPath(),
                    'variables' => array_values($variables),
                    'defaults'  => $defaults,
                ];
            } catch (\Exception $e) {
            }
        }

        $data = [
            'baseUrl'  => $request->getBaseUrl(),
            'basePath' => $request->getBasePath(),
            'routes'   => $routes,
        ];

        $content = sprintf('Phlexible.Router.setData(%s);', json_encode($data));

        return new Response($content, 200, ['Content-type' => 'text/javascript; charset=utf-8']);
    }
}
