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
     * @param int     $teaserId
     *
     * @return Response
     * @Route("/render/{teaserId}", name="teasers_teaser_render")
     */
    public function renderAction(Request $request, $teaserId)
    {
        $teaser = $this->get('phlexible_teaser.teaser_service')->findTeaser($teaserId);
        $language = $request->get('language', 'de');

        if ($teaser->getType() === 'element') {
            $request->attributes->set('language', $language);
            $request->attributes->set('contentDocument', $teaser);

            $renderConfigurator = $this->get('phlexible_element_renderer.configurator');
            $renderConfig = $renderConfigurator->configure($request);

            if ($request->get('template')) {
                $renderConfig->set('template', $request->get('template', 'teaser'));
            }

            $dataProvider = $this->get('phlexible_twig_renderer.data_provider');
            $templating = $this->get('templating');
            $data = $dataProvider->provide($renderConfig);
            $template = $renderConfig->get('template');

            return $templating->renderResponse($template, (array) $data);
        } elseif ($teaser->getType() === 'catch') {
            $catchRepository = $this->get('phlexible_teaser.teaser_service');
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
