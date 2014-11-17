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
 * Preview controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/frontend/preview")
 */
class PreviewController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     * @Route("", name="frontend_preview")
     */
    public function previewAction(Request $request)
    {
        $language = $request->get('language');
        $tid = $request->get('id');

        $contentTreeManager = $this->get('phlexible_tree.content_tree_manager.delegating');
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $tree = $contentTreeManager->findByTreeId($tid);
        $tree->setLanguage($language);
        $node = $tree->get($tid);

        $siteroot = $siterootManager->find($node->getTree()->getSiterootId());
        $siteroot->setContentChannels([1 => 1]);
        $siterootUrl = $siteroot->getDefaultUrl();

        $request->attributes->set('_locale', $language);
        $request->attributes->set('language', $language);
        $request->attributes->set('routeDocument', $node);
        $request->attributes->set('contentDocument', $node);
        $request->attributes->set('siterootUrl', $siterootUrl);
        $request->attributes->set('preview', true);

        $this->get('router.request_context')->setParameter('preview', true);

        $dataProvider = $this->get('phlexible_element_renderer.data_provider');
        $data = $dataProvider->provide($request);

        return $this->render($data['template'], (array) $data);
    }
}
