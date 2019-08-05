<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\EventListener;

use Phlexible\Bundle\ElementRendererBundle\Configurator\ConfiguratorInterface;
use Phlexible\Bundle\SiterootBundle\Siteroot\SiterootRequestMatcher;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

/**
 * Exception listener.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExceptionListener
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var ConfiguratorInterface
     */
    private $configurator;

    /**
     * @var ContentTreeManagerInterface
     */
    private $treeManager;

    /**
     * @var SiterootRequestMatcher
     */
    private $siterootRequestMatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param \Twig_Environment           $twig
     * @param ConfiguratorInterface       $configurator
     * @param ContentTreeManagerInterface $treeManager
     * @param SiterootRequestMatcher      $siterootRequestMatcher
     * @param LoggerInterface             $logger
     * @param bool                        $debug
     */
    public function __construct(
        \Twig_Environment $twig,
        ConfiguratorInterface $configurator,
        ContentTreeManagerInterface $treeManager,
        SiterootRequestMatcher $siterootRequestMatcher,
        LoggerInterface $logger = null,
        $debug = false)
    {
        $this->twig = $twig;
        $this->configurator = $configurator;
        $this->treeManager = $treeManager;
        $this->siterootRequestMatcher = $siterootRequestMatcher;
        $this->logger = $logger;
        $this->debug = $debug;
    }

    /**
     * Handles security related exceptions.
     *
     * @param GetResponseForExceptionEvent $event An GetResponseForExceptionEvent instance
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        static $handling;

        if (true === $handling) {
            return;
        }

        $handling = true;

        $exception = $event->getException();
        $this->logException($exception, sprintf('Uncaught PHP Exception %s: "%s" at %s line %s', get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine()));

        $request = $event->getRequest();

        // Only for debug
        if ($this->debug) {
            return;
        }

        // Not for xml http requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        $exception = $event->getException();
        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
        } else {
            $code = $exception->getCode();
        }
        if (!in_array($code, array(401, 403, 404, 500))) {
            $code = 500;
        }

        $tid = null;
        $siteroot = null;
        $template = null;

        // Only for phlexible tree nodes
        if (!$request->attributes->has('siterootUrl')) {
            $siteroot = $this->siterootRequestMatcher->matchRequest($request);
            if (!$siteroot) {
                return;
            }

            $request->attributes->set('siterootUrl', $siteroot->getDefaultUrl());
            $tid = $siteroot->getSpecialTid($request->getLocale(), "error_$code");
        }

        if ($this->twig->getLoader()->exists("::error/error-$code.html.twig")) {
            $template = "::error/error-$code.html.twig";
        } elseif ($this->twig->getLoader()->exists('::error/error.html.twig')) {
            $template = '::error/error.html.twig';
        }

        if (!$tid) {
            if (!$template) {
                return;
            }

            $content = $this->twig->render($template);
            $response = new Response($content, $code);

            $event->setResponse($response);

            return;
        }

        $treeNode = $this->treeManager->findByTreeId($tid)->get($tid);
        $request = $this->duplicateRequest($exception, $request, $tid, $treeNode, $template);

        try {
            $response = $event->getKernel()->handle($request, HttpKernelInterface::SUB_REQUEST, false);
        } catch (\Exception $e) {
            $this->logException($e, sprintf('Exception thrown when handling an exception (%s: %s at %s line %s)', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()), false);

            $wrapper = $e;

            while ($prev = $wrapper->getPrevious()) {
                if ($exception === $wrapper = $prev) {
                    throw $e;
                }
            }

            $prev = new \ReflectionProperty('Exception', 'previous');
            $prev->setAccessible(true);
            $prev->setValue($wrapper, $exception);

            throw $e;
        }

        $event->setResponse($response);
    }

    /**
     * Logs an exception.
     *
     * @param \Exception $exception The \Exception instance
     * @param string     $message   The error message to log
     */
    protected function logException(\Exception $exception, $message)
    {
        if (null !== $this->logger) {
            if (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
                $this->logger->critical($message, array('exception' => $exception));
            } else {
                $this->logger->error($message, array('exception' => $exception));
            }
        }
    }

    /**
     * Clones the request for the exception.
     *
     * @param \Exception        $exception the thrown exception
     * @param Request           $request   the original request
     * @param int               $tid
     * @param TreeNodeInterface $treeNode
     * @param string            $template
     *
     * @return Request $request the cloned request
     */
    protected function duplicateRequest(\Exception $exception, Request $request, $tid, $treeNode, $template)
    {
        $attributes = array(
            'tid' => $tid,
            'siterootUrl' => $request->attributes->get('siterootUrl'),
            'routeDocument' => $treeNode,
            'contentDocument' => $treeNode,
            '_controller' => 'PhlexibleCmsBundle:Online:index',
            'exception' => FlattenException::create($exception),
            'logger' => $this->logger instanceof DebugLoggerInterface ? $this->logger : null,
            // keep for BC -- as $format can be an argument of the controller callable
            // see src/Symfony/Bundle/TwigBundle/Controller/ExceptionController.php
            // @deprecated since version 2.4, to be removed in 3.0
            'format' => $request->getRequestFormat(),
        );
        if ($template) {
            $attributes['template'] = $template;
        }
        $request = $request->duplicate(null, null, $attributes);
        $request->setMethod('GET');

        return $request;
    }
}
