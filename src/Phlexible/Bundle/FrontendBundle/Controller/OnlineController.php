<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Online controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class OnlineController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/", name="frontend_online")
     */
    public function indexAction(Request $request)
    {
        $configurator = $this->get('phlexible_element_renderer.configurator');
        $configuration = $configurator->configure($request);
        if ($configuration->hasResponse()) {
            return $configuration->getResponse();
        }
        $data = $configuration->getVariables();

        $template = $data['template'];
        if ($request->attributes->has('template')) {
            $template = $request->attributes->get('template');
        }

        return $this->render($template, (array) $data);
    }
}
