<?php

namespace Phlexible\Bundle\TaskBundle\Finite\Loader;

use Finite\Event\CallbackHandler;
use Finite\Loader\LoaderInterface;
use Finite\State\State;
use Finite\State\StateInterface;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachineInterface;
use Finite\Transition\Transition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

/**
 * Loads a StateMachine from a yaml file
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class YamlFileLoader implements LoaderInterface
{
    /**
     * @var CallbackHandler
     */
    private $callbackHandler;

    /**
     * @var array
     */
    private $config;

    /**
     * @param string          $yamlFile
     * @param CallbackHandler $handler
     */
    public function __construct($yamlFile, CallbackHandler $handler = null)
    {
        $this->callbackHandler = $handler;

        $yaml = new Yaml();
        $config = $yaml->parse(file_get_contents($yamlFile));
        $this->config = array_merge(
            [
                'class'       => '',
                'states'      => [],
                'transitions' => [],
            ],
            $config
        );
    }

    /**
     * {@inheritDoc}
     */
    public function load(StateMachineInterface $stateMachine)
    {
        if (null === $this->callbackHandler) {
            $this->callbackHandler = new CallbackHandler($stateMachine->getDispatcher());
        }
        $this->loadStates($stateMachine);
        $this->loadTransitions($stateMachine);
        $this->loadCallbacks($stateMachine);
    }

    /**
     * {@inheritDoc}
     */
    public function supports(StatefulInterface $object)
    {
        $reflection = new \ReflectionClass($this->config['class']);

        return $reflection->isInstance($object);
    }

    /**
     * @param StateMachineInterface $stateMachine
     */
    private function loadStates(StateMachineInterface $stateMachine)
    {
        $resolver = new OptionsResolver;
        $resolver->setDefaults(['type' => StateInterface::TYPE_NORMAL, 'properties' => []]);
        $resolver->setAllowedValues(
            [
                'type' => [
                    StateInterface::TYPE_INITIAL,
                    StateInterface::TYPE_NORMAL,
                    StateInterface::TYPE_FINAL
                ]
            ]
        );

        foreach ($this->config['states'] as $state => $config) {
            $config = $resolver->resolve($config);
            $stateMachine->addState(new State($state, $config['type'], [], $config['properties']));
        }
    }

    /**
     * @param StateMachineInterface $stateMachine
     */
    private function loadTransitions(StateMachineInterface $stateMachine)
    {
        $resolver = new OptionsResolver;
        $resolver->setRequired(['from', 'to']);
        $resolver->setOptional(['guard']);
        $resolver->setNormalizers(
            [
                'from' => function (Options $options, $v) {
                    return (array) $v;
                },
                'guard' => function (Options $options, $v) {
                    return !isset($v) ? null : $v;
                }
            ]
        );

        foreach ($this->config['transitions'] as $transition => $config) {
            $config = $resolver->resolve($config);
            $stateMachine->addTransition(new Transition($transition, $config['from'], $config['to'], $config['guard']));
        }
    }

    /**
     * @param StateMachineInterface $stateMachine
     */
    private function loadCallbacks(StateMachineInterface $stateMachine)
    {
        if (!isset($this->config['callbacks'])) {
            return;
        }

        foreach (['before', 'after'] as $position) {
            $this->loadCallbacksFor($position, $stateMachine);
        }
    }

    /**
     * @param string                $position
     * @param StateMachineInterface $stateMachine
     */
    private function loadCallbacksFor($position, StateMachineInterface $stateMachine)
    {
        if (!isset($this->config['callbacks'][$position])) {
            return;
        }

        $method = 'add'.ucfirst($position);
        foreach ($this->config['callbacks'][$position] as $specs) {
            $callback = $specs['do'];
            unset($specs['do']);

            $this->callbackHandler->$method($stateMachine, $callback, $specs);
        }
    }
}
