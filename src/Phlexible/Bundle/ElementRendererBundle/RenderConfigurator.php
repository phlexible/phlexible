<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle;

use Phlexible\Bundle\ElementRendererBundle\RenderConfigurator\ConfiguratorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Element render configurator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RenderConfigurator
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
     * @var ConfiguratorInterface[]
     */
    private $configurators = array();

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
     * @param ConfiguratorInterface $configurator
     *
     * @return $this
     */
    public function addConfigurator(ConfiguratorInterface $configurator)
    {
        $this->configurators[] = $configurator;

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return RenderConfiguration
     */
    public function configure(Request $request)
    {
        $renderConfiguration = new RenderConfiguration();
        $renderConfiguration->set('request', $request);

        // Before Init Event
        /*
        $beforeEvent = new \Makeweb_Renderers_Event_BeforeInit($this);
        if (!$this->dispatcher->dispatch($beforeEvent))
        {
            return $renderConfiguration;
        }
        */

        foreach ($this->configurators as $configurator) {
            $configurator->configure($request, $renderConfiguration);
        }

        // Init Event
        /*
        $event = new \Makeweb_Renderers_Event_Init($this);
        $this->dispatcher->dispatch($event);
        */

        return $renderConfiguration;
    }

}