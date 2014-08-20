<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Preview controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/frontend/preview")
 */
class PreviewController extends Controller
{
    public function previewAction()
    {
        $request = Request::createFromGlobals();

        $router = $this->getContainer()->get('elementRendererRequestRouter');
        $router->getRequestContext()->setBaseUrl($request->getBaseUrl());

        $renderRequest = $router->match($request);

        $renderConfigurator = $this->getContainer()->get('elementrenderer.configurator');
        $renderConfig = $renderConfigurator->configure($renderRequest);

        $renderer = $this->getContainer()->get('twigrenderer.renderer');
        $content = $renderer->render($renderConfig);
        $this->_response->setBody($content);

        return;

        ini_set('display_errors', 1);

        // get dispatcher
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        // get response
        $response = $this->getResponse();

        try {
            // create request
            $requestHandler = Makeweb_Frontend_Request_Handler::getPreviewHandler();
            $requestHandler->setPathPrefix($this->getRequest()->getBaseUrl() . '/preview');
            $request = new Makeweb_Frontend_Request($response, null, $requestHandler);
        } catch (Makeweb_Frontend_Request_Exception $e) {
            if ($this->getContainer()->get('application')->getDebug()) {
                echo 'Whoops';
                exit(1);
            }

            MWF_Log::exception($e);

            $response
                ->setHttpResponseCode(404)
                ->setBody('Page not found.');

            return;
        } catch (Exception $e) {
            if ($this->getContainer()->get('application')->getDebug()) {
                echo 'Whoops';
                exit(1);
            }

            MWF_Log::exception($e);

            $response
                ->setHttpResponseCode(500)
                ->setBody('Error occured.');

            return;
        }

        try {
            if (!$response->isRedirect()) {
                $renderer = $this->getContainer()->get('renderersHtml');

                $event = new Makeweb_Frontend_Event_InitRenderer($renderer);
                $dispatcher->dispatch($event);

                $renderer->render($request, $response);
            }
        } catch (Exception $e) {
            MWF_Log::exception($e);
            FirePHP::getInstance(true)->error($e);

            $params = array(
                'exception' => $e,
                'request'   => $request,
            );

            $this->_forward('index', 'error', null, $params);
        }
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
        $router = $this->get('phlexible_tree.router');

        $node = $treeManager->getByNodeId($tid)->get($tid);

        $urls = array(
            'preview' => '',
            'online'  => '',
        );

        if ($node) {
            $urls['preview'] = $router->generate($node);

            if (1 || $node->isPublished($language)) {
                $urls['online'] = $router->generate($node);
            }
        }

        return new ResultResponse(true, '', $urls);
    }
}
