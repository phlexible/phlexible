<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\CmsBundle\Controller;

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
