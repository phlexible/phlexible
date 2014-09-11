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
        $dataProvider = $this->get('phlexible_element_renderer.data_provider');
        $data = $dataProvider->provide($request);

        return $this->render($data['template'], (array) $data);
    }

    public function oldAction()
    {
        // get dispatcher
        $dispatcher = $this->get('event_dispatcher');

        // get response
        $response = $this->getResponse();

        try {
            // create request
            $request = new Makeweb_Frontend_Request($response, null, false, $this->getRequest()->getBaseUrl());
        } catch (Makeweb_Elements_Context_Exception $e) {
            Brainbits_Debug_Error::outputException($e);
            $this->_response->setHttpResponseCode(500);

            return;
        } catch (Makeweb_Frontend_Request_Exception $e) {
            if ($this->getContainer()->get('application')->getDebug()) {
                echo 'whoops';
                exit(1);
            }

            MWF_Log::exception($e);

            $response
                ->setHttpResponseCode(404)
                ->setBody('Page not found.');

            return;
        } catch (Exception $e) {
            if ($this->getContainer()->get('application')->getDebug()) {
                echo 'whoops';
                exit(1);
            }

            MWF_Log::exception($e);
            MWF_Log::firephp($e);

            echo $e->getMessage() . '<pre>' . $e->getTraceAsString();
            die;
            $response
                ->setHttpResponseCode(500)
                ->setBody('Error occured.');

            return;
        }

        try {
            if ($response->isException()) {
                $e = current($response->getException());
                throw ($e);
            }

            if (!$response->isRedirect()) {
                // Set the frontend user
                $frontendUser = new Makeweb_Frontend_User();
                $frontendUser->setInterfaceLanguage($request->getLanguage());
                MWF_Env::setUser($frontendUser);

                $renderer = $this->getContainer()->renderersHtml;

                $event = new Makeweb_Frontend_Event_InitRenderer($renderer);
                $dispatcher->dispatch($event);

                $renderer->render($request, $response);
            }
        } catch (Makeweb_Elements_Context_Exception $e) {
            MWF_Log::exception($e);

            if ($request->hasContext()) {
                $container = $this->getContainer();
                $country = $request->getContext()->getCountry();
                if (isset(MWF_Registry::getConfig()->context->defaults) &&
                    isset(MWF_Registry::getConfig()->context->defaults->{$country})
                ) {
                    $languages = $container->config->context->defaults->{$country}->toArray();
                    $request->setLanguage(current($languages));
                } elseif ($request->getLanguage()) {

                } else {
                    $language = $container->getParam(':phlexible_cms.languages.default');
                    $request->setLanguage($language);
                }
            }

            $params = array(
                'exception' => $e,
                'request'   => $request,
            );

            $this->forward('index', 'error', null, $params);

            return;
        } catch (Exception $e) {
            MWF_Log::exception($e);

            $params = array(
                'exception' => $e,
                'request'   => $request,
            );

            $this->forward('index', 'error', null, $params);

            return;
        }
    }
}
