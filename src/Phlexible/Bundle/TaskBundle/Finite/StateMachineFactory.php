<?php

namespace Phlexible\Bundle\TaskBundle\Finite;

use Finite\StateMachine\StateMachine;
use Phlexible\Bundle\TaskBundle\Finite\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Loads a StateMachine from a yaml file
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class StateMachineFactory
{
    /**
     * @param FileLocatorInterface     $locator
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $yamlFile
     *
     * @return StateMachine
     */
    public static function factory(FileLocatorInterface $locator, EventDispatcherInterface $eventDispatcher, $yamlFile)
    {
        $stateMachine = new StateMachine(null, $eventDispatcher);

        $loader = new YamlFileLoader($locator->locate($yamlFile));
        $loader->load($stateMachine);

        return $stateMachine;
    }
}
