<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Config;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;
use Phlexible\Bundle\GuiBundle\GuiEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Config builder.
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
     * Gather configs and return config array.
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
