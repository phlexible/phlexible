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
use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeContext;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Navigation configurator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NavigationConfigurator implements ConfiguratorInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     */
    public function __construct(EventDispatcherInterface $dispatcher, LoggerInterface $logger)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, Configuration $renderConfiguration)
    {
        if (!$renderConfiguration->hasFeature('treeNode')) {
            return;
        }

        /** @var Url $siterootUrl */
        $siterootUrl = $request->attributes->get('siterootUrl');

        $navigations = [];

        foreach ($siterootUrl->getSiteroot()->getNavigations() as $siterootNavigation) {
            $startTid = $siterootNavigation->getStartTreeId();
            $currentTreeNode = $treeNode = $renderConfiguration->get('treeNode');
            if ($startTid) {
                $treeNode = $currentTreeNode->getTree()->get($startTid);
            }

            $navigations[$siterootNavigation->getTitle()] = new ContentTreeContext(
                $treeNode,
                $currentTreeNode,
                $siterootNavigation->getMaxDepth()
            );
        }

        $renderConfiguration
            ->addFeature('navigation')
            ->setVariable('navigation', $navigations);

        $event = new ConfigureEvent($renderConfiguration);
        $this->dispatcher->dispatch(ElementRendererEvents::CONFIGURE_NAVIGATION, $event);
    }
}
