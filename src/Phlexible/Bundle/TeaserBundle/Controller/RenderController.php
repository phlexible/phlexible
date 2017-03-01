<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Render controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/_teaser/render")
 */
class RenderController extends Controller
{
    /**
     * Render action.
     *
     * @param Request $request
     * @param int     $teaserId
     *
     * @return Response
     * @Route("/{_locale}/{teaserId}", name="teaser_render")
     */
    public function htmlAction(Request $request, $teaserId)
    {
        $requestStack = $this->get('request_stack');
        $masterRequest = $requestStack->getMasterRequest();

        if ($request->get('preview') || $request->get('_preview')) {
            $request->attributes->set('_preview', true);
        } elseif ($masterRequest !== $request && $masterRequest->attributes->get('_preview')) {
            $request->attributes->set('_preview', true);
        }

        $teaser = $this->get('phlexible_teaser.teaser_service')->find($teaserId);

        $request->attributes->set('contentDocument', $teaser);

        $renderConfigurator = $this->get('phlexible_element_renderer.configurator');
        $renderConfig = $renderConfigurator->configure($request);

        if ($renderConfig->getResponse()) {
            return $renderConfig->getResponse();
        }

        if ($request->get('template')) {
            $renderConfig->set('template', $request->get('template', 'teaser'));
        }

        $data = $renderConfig->getVariables();

        return $this->render($data['template'], (array) $data);
    }
}
