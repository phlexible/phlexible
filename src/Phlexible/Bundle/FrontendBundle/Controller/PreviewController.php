<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendBundle\Controller;

use Phlexible\Bundle\ContentchannelBundle\Entity\Contentchannel;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\SiterootBundle\Entity\Url;
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

        $node = $contentTreeManager->findByTreeId($tid)->get($tid);

        $siteroot = $siterootManager->find($node->getTree()->getSiterootId());
        $siteroot->setContentChannels(array(1 => 1));
        $siterootUrl = $siteroot->getDefaultUrl();

        $request->attributes->set('language', $language);
        $request->attributes->set('routeDocument', $node);
        $request->attributes->set('contentDocument', $node);
        $request->attributes->set('siterootUrl', $siterootUrl);
        $request->attributes->set('preview', true);

        $this->get('router.request_context')->setParameter('preview', true);

        $renderConfigurator = $this->get('phlexible_element_renderer.configurator');
        $renderConfig = $renderConfigurator->configure($request);

        $dataProvider = $this->get('phlexible_twig_renderer.data_provider');
        $templating = $this->get('templating');
        $data = $dataProvider->provide($renderConfig);
        $template = $renderConfig->get('template');

        return $templating->renderResponse($template, (array) $data);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/urls", name="frontend_preview_urls")
     */
    public function urlsAction(Request $request)
    {
        $tid = $request->get('tid');
        $language = $request->get('language');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $stateManager = $this->get('phlexible_tree.state_manager');

        $node = $treeManager->getByNodeId($tid)->get($tid);

        $urls = array(
            'preview' => '',
            'online'  => '',
        );

        if ($node) {
            $urls['preview'] = $this->generateUrl('frontend_preview', array('id' => $tid, 'language' => $language));

            if ($stateManager->isPublished($node, $language)) {
                try {
                    //$urls['online'] = $this->generateUrl($node);
                } catch (\Exception $e) {

                }
            }
        }

        return new ResultResponse(true, '', $urls);
    }
}
