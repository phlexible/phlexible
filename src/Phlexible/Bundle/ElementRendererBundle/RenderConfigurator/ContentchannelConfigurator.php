<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\RenderConfigurator;

use Phlexible\Bundle\ContentchannelBundle\Contentchannel\ContentchannelRepository;
use Phlexible\Bundle\ContentchannelBundle\Model\ContentchannelManagerInterface;
use Phlexible\Bundle\ElementRendererBundle\RenderConfiguration;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Content channel configurator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentchannelConfigurator implements ConfiguratorInterface
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
     * @var ContentchannelManagerInterface
     */
    private $contentchannelManager;

    /**
     * @param EventDispatcherInterface       $dispatcher
     * @param LoggerInterface                $logger
     * @param ContentchannelManagerInterface $contentchannelManager
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        ContentchannelManagerInterface $contentchannelManager)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->contentchannelManager = $contentchannelManager;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, RenderConfiguration $renderConfiguration)
    {
        if (!$request->attributes->has('siterootUrl')) {
            return;
        }

        // Before Init View Event
        /*
        $beforeEvent = new \Makeweb_Renderers_Event_BeforeInitContentChannel($this);
        if (!$this->dispatcher->dispatch($beforeEvent))
        {
            return false;
        }
        */

        $contentchannel = $this->contentchannelManager->find(
            $request->attributes->get('siterootUrl')->getSiteroot()->getDefaultContentChannelId()
        );
        $renderConfiguration
            ->addFeature('contentchannel')
            ->set('contentchannel', $contentchannel);

        // Init View Event
        /*
        $event = new \Makeweb_Renderers_Event_InitContentChannel($this);
        $this->dispatcher->dispatch($event);
        */
    }

}
