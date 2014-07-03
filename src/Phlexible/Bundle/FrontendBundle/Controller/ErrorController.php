<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Error controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Marcus Stöhr <mstoehr@brainbits.net>
 */
class ErrorController extends Controller
{
    public function errorAction()
    {
        return $this->indexAction();
    }

    public function indexAction()
    {
        if ($this->hasParam('exception') && $this->getContainer()->getParameter('kernel.debug')) {
            echo 'whoops';
            exit(1);
        }

        try {
            /* @var $request Makeweb_Frontend_Request */
            if (!$this->hasParam('request')) {
                $this->getResponse()
                    ->setHttpResponseCode(500)
                    ->setBody('Error occured.');

                return;
            }

            $request = $this->_getParam('request');
            $language = $request->getLanguage();

            $specialTid = 'error_500';

            if ($this->hasParam('exception')) {
                $exception = $this->_getParam('exception');
                $code = $exception->getCode();

                if ($code && is_int($code)) {
                    $specialTid = 'error_' . $code;
                }
            }

            $tid = $request->getSiteRoot()->getSpecialTid($language, $specialTid);
            $request->setTid($tid);
            if ($request->isOnlineRequest()) {
                $request->setVersionOnline();
            } else {
                $request->setVersionLatest();
            }

            $renderer = $this->getContainer()->get('renderersHtml');
            $renderer->render($request, $this->getResponse());

            return;
        } catch (\Exception $e) {
            if ($this->getContainer()->getParameter('kernel.debug')) {
                Brainbits_Debug_Error::outputException($e);

                return;
            }

            MWF_Log::exception($e);
            FirePhp::getInstance(true)->log($e);

            // write message
            $subject = 'Error 500 occured (TID: ' . $tid . ')';
            $body = 'An server error occurred with the following message:' . PHP_EOL . $e->getMessage()
                . PHP_EOL . 'with the following stack trace: ' . PHP_EOL . $e->getTraceAsString();

            $message = new Makeweb_Frontend_Message(
                $subject,
                $body,
                MWF_Core_Messages_Message::PRIORITY_URGENT,
                MWF_Core_Messages_Message::TYPE_ERROR
            );
            $message->post();
        }

        echo $e->getMessage() . '<pre>' . $e->getTraceAsString();
        die;

        $this->getResponse()->setHttpResponseCode(500)
            ->setBody('Error occured.');
    }
}