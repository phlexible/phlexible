<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Config;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;
use Phlexible\Bundle\GuiBundle\GuiEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Config builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ConfigBuilder
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Gather configs and return config array
     *
     * @return array
     */
    public function build()
    {
        $config = new Config();

        $event = new GetConfigEvent($config);
        $this->eventDispatcher->dispatch(GuiEvents::GET_CONFIG, $event);

        return $config;
    }
}
