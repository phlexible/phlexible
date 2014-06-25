<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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

        try
        {
            // create request
            $requestHandler = Makeweb_Frontend_Request_Handler::getPreviewHandler();
            $requestHandler->setPathPrefix($this->getRequest()->getBaseUrl() . '/preview');
            $request = new Makeweb_Frontend_Request($response, null, $requestHandler);
        }
        catch (Makeweb_Frontend_Request_Exception $e)
        {
            if ($this->getContainer()->get('application')->getDebug()) {
                echo 'Whoops';
                exit(1);
            }

            MWF_Log::exception($e);

            $response
                ->setHttpResponseCode(404)
                ->setBody('Page not found.');

            return;
        }
        catch (Exception $e)
        {
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

        try
        {
            if (!$response->isRedirect())
            {
                $renderer = $this->getContainer()->get('renderersHtml');

                $event = new Makeweb_Frontend_Event_InitRenderer($renderer);
                $dispatcher->dispatch($event);

                $renderer->render($request, $response);
            }
        }
        catch (Exception $e)
        {
            MWF_Log::exception($e);
            FirePHP::getInstance(true)->error($e);

            $params = array(
                'exception' => $e,
                'request'   => $request,
            );

            $this->_forward('index', 'error', null, $params);
        }
    }

    public function debugAction()
    {
        try
        {
            ini_set('display_errors', 1);

            // get dispatcher
            $dispatcher = $this->getContainer()->get('event_dispatcher');

            // get response
            $response = $this->getResponse();

            // define request
            $requestHandler = Makeweb_Frontend_Request_Handler::getDebugHandler();
            $request = new Makeweb_Frontend_Request($response, null, $requestHandler);
            $request->setParam('debug', 1);

            if (!$response->isRedirect())
            {
//                $frontendUser = new Makeweb_Frontend_User();
//                $frontendUser->setInterfaceLanguage($request->getLanguage());
//                MWF_Env::setUser($frontendUser);

                $rendererClassname = $request->getContentChannel()->getRendererClassname();
                $renderer = new $rendererClassname();
                $renderer->setRequest($request);
                $renderer->setResponse($response);

                $event = new Makeweb_Frontend_Event_InitRenderer($renderer);
                $dispatcher->dispatch($event);

                ob_start();
                $renderer->render();
                ob_end_clean();

                if ($response->isRedirect())
                {
                    $this->_displayRedirect($response);
                }
            }
            else
            {
                $this->_displayRedirect($response);
            }
        }
        catch (Exception $e)
        {
            while (ob_get_status())
            {
                ob_end_clean();
            }

            Brainbits_Debug_Error::outputException($e);
            die;
        }
    }

    /**
     * @Route("/urls", name="frontend_preview_urls")
     */
    public function urlsAction()
    {
        $tid = $this->getParam('tid');
        $language = $this->getParam('language');

        $node = $this->getContainer()->get('phlexible_tree.manager')->getByNodeId($tid)->get($tid);

        $urls = array(
            'preview' => '',
            'online'  => '',
            'debug'   => '',
        );

        if ($node) {
            $urls['preview'] = $this->getContainer()->get('phlexible_tree.router')->generate($node);
            $urls['debug']   = str_replace('/preview/', '/debug/', $urls['preview']);

            if (1 || $node->isPublished($language)) {
                $urls['online'] = $this->getContainer()->get('phlexible_tree.router')->generate($node);
            }
        }

        $this->_response->setResult(true, $tid, '', $urls);
    }
}
