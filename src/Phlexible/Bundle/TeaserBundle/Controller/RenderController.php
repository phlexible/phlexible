<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Render controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/_teaser")
 */
class RenderController extends Controller
{
    /**
     * Render action
     *
     * @param Request $request
     * @param int     $teaserId
     *
     * @return Response
     * @Route("/render/{teaserId}", name="teaser_render")
     */
    public function htmlAction(Request $request, $teaserId)
    {
        $teaser = $this->get('phlexible_teaser.teaser_service')->find($teaserId);
        $language = $request->get('language', 'de');

        $request->attributes->set('language', $language);
        $request->attributes->set('contentDocument', $teaser);

        $renderConfigurator = $this->get('phlexible_element_renderer.configurator');
        $renderConfig = $renderConfigurator->configure($request);

        if ($request->get('template')) {
            $renderConfig->set('template', $request->get('template', 'teaser'));
        }

        $data = $renderConfig->getVariables();

        return $this->render($data['template'], (array) $data);
    }
}
