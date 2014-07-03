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
use Phlexible\Bundle\GuiBundle\Menu\MenuLoader;
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
     * @return Response
     * @Route("", name="gui_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction(Request $request)
    {
        $viewIndex = $this->get('phlexible_gui.view.index');

        $securityContext = $this->get('security.context');
        $user = $this->getUser();

        return array(
            'baseUrl'        => $request->getBaseUrl() . '/admin',
            'basePath'       => $request->getBasePath() . '/admin',
            'componentsPath' => '/bundles',
            'extPath'        => $request->getBasePath() . '/bundles/phlexiblegui/scripts/ext-2.3.0/',
            'debug'          => $this->container->getParameter('kernel.debug'),
            'theme'          => $user->getProperty('theme', 'default'),
            'language'       => $user->getInterfaceLanguage('en'),
            'appTitle'       => $this->container->getParameter('app.app_title'),
            'appVersion'     => $this->container->getParameter('app.app_version'),
            'appUrl'         => $this->container->getParameter('app.app_url'),
            'projectTitle'   => $this->container->getParameter('app.project_title'),
            'scripts'        => $viewIndex->get($request, $securityContext),
            'noScript'       => $viewIndex->getNoScript(
                $request->getBaseUrl(),
                $this->container->getParameter('app.app_title'),
                $this->container->getParameter('app.project_title')
            ),
        );
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

        $routes = array();
        foreach ($router->getRouteCollection() as $id => $route) {
            /* @var $route RoutingRoute */

            $compiledRoute = $route->compile();
            $variables = $compiledRoute->getVariables();
            $placeholders = array();
            foreach ($variables as $variable) {
                $placeholders[$variable] = '{' . $variable . '}';
            }

            $routeDefaults = $route->getDefaults();
            $defaults = array();
            foreach ($routeDefaults as $key => $default) {
                if (!in_array($key, $variables)) {
                    continue;
                }
                $defaults[$key] = $default;
            }

            try {
                $routes[$id] = array(
                    'path'      => $request->getBaseUrl() . $route->getPath(),
                    'variables' => array_values($variables),
                    'defaults'  => $defaults,
                );
            } catch (\Exception $e) {
            }
        }

        $data = array(
            'baseUrl'  => $request->getBaseUrl(),
            'basePath' => $request->getBasePath(),
            'routes'   => $routes,
        );

        $content = sprintf('Phlexible.Router.setData(%s);', json_encode($data));

        return new Response($content, 200, array('Content-type' => 'text/javascript; charset=utf-8'));
    }
}
