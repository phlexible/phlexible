<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Frame controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FrameController extends Controller
{
    /**
     * Render Frame.
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

        return [
            'scripts' => $viewIndex->get($request),
            'noScript' => $viewIndex->getNoScript(),
        ];
    }

    /**
     * Return configuration.
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
        $configBuilder = $this->get('phlexible_gui.config_builder');
        $config = $configBuilder->build();

        return new JsonResponse($config->all());
    }

    /**
     * Return menu.
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
        $items = $loader->load();
        $data = $items->toArray();

        return new JsonResponse($data);
    }

    /**
     * Return routes.
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
        $routeExtractor = $this->get('phlexible_gui.route_extractor');
        $extractedRoutes = $routeExtractor->extract($request);

        $content = sprintf('Phlexible.Router.setData(%s);', json_encode(array(
            'baseUrl' => $extractedRoutes->getBaseUrl(),
            'basePath' => $extractedRoutes->getBasePath(),
            'routes' => $extractedRoutes->getRoutes(),
        )));

        return new Response($content, 200, ['Content-type' => 'text/javascript; charset=utf-8']);
    }
}
