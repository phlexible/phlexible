<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\RenderConfigurator;

use Phlexible\Bundle\ElementRendererBundle\RenderConfiguration;
use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeContext;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Template configurator
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
    public function configure(Request $request, RenderConfiguration $renderConfiguration)
    {
        if (!$renderConfiguration->hasFeature('treeNode')) {
            return;
        }

        // Before Init Navigation Event
        /*
        $beforeEvent = new \Makeweb_Renderers_Event_BeforeInitNavigation($this);
        if (!$this->dispatcher->dispatch($beforeEvent))
        {
            return false;
        }
        */

        /** @var Url $siterootUrl */
        $siterootUrl = $request->attributes->get('siterootUrl');

        $navigations = array();

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

        // Init Navigation Event
        /*
        $event = new \Makeweb_Renderers_Event_InitNavigation($this);
        $this->dispatcher->dispatch($event);
        */

        $renderConfiguration
            ->addFeature('navigation')
            ->set('navigations', $navigations);
    }

}
