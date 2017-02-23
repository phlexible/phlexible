<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Configurator;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElementLoader;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementRendererBundle\Configurator\ConfiguratorInterface;
use Phlexible\Bundle\ElementRendererBundle\Configurator\Configuration;
use Phlexible\Bundle\ElementRendererBundle\ElementRendererEvents;
use Phlexible\Bundle\ElementRendererBundle\Event\ConfigureEvent;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Element configurator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementConfigurator implements ConfiguratorInterface
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
     * @var ElementService
     */
    private $elementService;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var ContentElementLoader
     */
    private $loader;


    /**
     * @param EventDispatcherInterface      $dispatcher
     * @param LoggerInterface               $logger
     * @param ElementService                $elementService
     * @param ContentElementLoader          $loader
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        ElementService $elementService,
        ContentElementLoader $loader,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->elementService = $elementService;
        $this->loader = $loader;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, Configuration $renderConfiguration)
    {
        if (!$renderConfiguration->hasFeature('eid')) {
            return;
        }

        $elementEid = $renderConfiguration->get('eid');
        $elementVersion = $renderConfiguration->get('version');
        $elementLanguage = $renderConfiguration->get('language');

        /*
        $versionStrategy = new OnlineVersionStrategy($this->elementService);
        $availableLanguages = $request->attributes->get('availableLanguages', array('de'));
        $elementLanguage = $versionStrategy->findLanguage($request, $element, $availableLanguages);
        */

        $contentElement = $this->loader->load(
            $elementEid,
            $elementVersion,
            $elementLanguage
        );

        if (!$contentElement) {
            return false;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $mappedField = $contentElement->getMappedField();
        if ($mappedField) {
            $forwardField = $mappedField->getForward();

            if ($forwardField) {
                $forward = json_decode($forwardField);
                $renderConfiguration->addFeature('forward')->setVariable('forward', $forward);

                return;
            }
        }

        $renderConfiguration
            ->addFeature('element')
            ->setVariable('contentElement', $contentElement)
            ->setVariable('content', $contentElement->getStructure());

        if (!$renderConfiguration->hasFeature('template')) {
            if ($contentElement->getElementtypeTemplate()) {
                $template = $contentElement->getElementtypeTemplate();
            } else {
                $template = '::' . $contentElement->getElementtypeUniqueId() . '.html.twig';
            }

            $renderConfiguration
                ->addFeature('template')
                ->setVariable('template', $template);
        }

        $event = new ConfigureEvent($renderConfiguration);
        $this->dispatcher->dispatch(ElementRendererEvents::CONFIGURE_ELEMENT, $event);
    }
}
