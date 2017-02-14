<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Configurator;

use Phlexible\Bundle\ElementRendererBundle\Configurator\ConfiguratorInterface;
use Phlexible\Bundle\ElementRendererBundle\Configurator\Configuration;
use Phlexible\Bundle\ElementRendererBundle\ElementRendererEvents;
use Phlexible\Bundle\ElementRendererBundle\Event\ConfigureEvent;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * Forward configurator
 *
 * @author Jens Schulze
 */
class ForwardConfigurator implements ConfiguratorInterface {
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ContentTreeManagerInterface
     */
    private $contentTreeManager;


    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface $logger
     * @param RouterInterface $router
     * @param ContentTreeManagerInterface $contentTreeManager
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        RouterInterface $router,
        ContentTreeManagerInterface $contentTreeManager
    ) {
        $this->dispatcher         = $dispatcher;
        $this->logger             = $logger;
        $this->router             = $router;
        $this->contentTreeManager = $contentTreeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, Configuration $renderConfiguration) {
        if (!$renderConfiguration->hasFeature('forward')) {
            return;
        }

        $forward = $renderConfiguration->getVariable('forward');

        switch ($forward->type) {
            case 'internal':
            case 'intrasiteroot':
                $contentTreeNode = $this->contentTreeManager->findByTreeId($forward->tid)->get($forward->tid);
                $url             = $this->router->generate($contentTreeNode);
                break;
            case 'external':
                $url = $forward->url;
                break;
            default:
                $url = NULL;
        }
        if ($url) {
            $thisUrl = $request->getBaseUrl() . $request->getPathInfo();
//            if ($url !== $thisUrl) {
            $renderConfiguration->setResponse(new RedirectResponse($url));
//            }
        }

//        $event = new ConfigureEvent($renderConfiguration);
//        $this->dispatcher->dispatch(ElementRendererEvents::CONFIGURE_XXXXXXXXXX, $event);
    }
}
