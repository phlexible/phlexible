<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Teaser controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/_teaser")
 */
class TeaserController extends Controller
{
    /**
     * Render action
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/render/{id}", name="teasers_teaser_render")
     */
    public function renderAction(Request $request)
    {
        $teaserId = $request->get('teaserId');
        $teaser = $this->get('phlexible_teaser.service')->findTeaser($teaserId);
        $language = 'de';

        if ($teaser->getType() === 'element') {
            $request = Request::createFromGlobals();
            $request->attributes->set('language', $language);

            $renderConfigurator = $this->get('phlexible_element_renderer.configurator');
            $renderConfig = $renderConfigurator->configure($request, $teaser);

            if ($request->has('template')) {
                $renderConfig->set('template', $request->get('template', 'teaser'));
            }

            $renderer = $this->get('phlexible_dwoo_renderer.renderer');
            $content = $renderer->render($renderConfig);
        } elseif ($teaser->getType() === 'catch') {
            $catchRepository = $this->get('phlexible_teaser.service');
            $catcher = $this->get('phlexible_teaser.catcher');
            $catch = $catchRepository->find($teaser->getTypeId());

            $resultPool = $catcher->catchElements($catch, array($language), false);

            $request = Request::createFromGlobals();
            $request->attributes->set('language', $language);

            $renderConfigurator = $this->get('phlexible_element_renderer.configurator');
            $renderConfig = $renderConfigurator->configure($request, $resultPool);

            if ($request->get('template')) {
                $renderConfig->set('template', $request->get('template', 'catch'));
            }

            $renderer = $this->get('phlexible_dwoo_renderer.renderer');
            $content = $renderer->render($renderConfig);
        } else {
            $content = 'what?';
        }

        return new Response($content);
    }
}
