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
 */
class PreviewController extends Controller
{
    /**
     * @param Request $request
     * @param int     $treeId
     *
     * @return Response
     */
    public function previewAction(Request $request, $treeId)
    {
        $language = $request->get('_locale');

        $contentTreeManager = $this->get('phlexible_tree.content_tree_manager.delegating');
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $tree = $contentTreeManager->findByTreeId($treeId);
        $tree->setLanguage($language);
        $node = $tree->get($treeId);

        $siteroot = $siterootManager->find($node->getTree()->getSiterootId());
        $siterootUrl = $siteroot->getDefaultUrl();

        $request->setLocale($language);
        $request->attributes->set('routeDocument', $node);
        $request->attributes->set('contentDocument', $node);
        $request->attributes->set('siterootUrl', $siterootUrl);
        $request->attributes->set('_preview', true);

        $node->getTree()->setPreview(true);
        $this->get('router.request_context')->setParameter('_preview', true);

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
