<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Configurator;

use Phlexible\Bundle\AccessControlBundle\Rights as ContentRightsManager;
use Phlexible\Bundle\ElementBundle\ContentElement\ContentElement;
use Phlexible\Bundle\ElementBundle\ContentElement\ContentElementLoader;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementRendererBundle\Configurator\ConfiguratorInterface;
use Phlexible\Bundle\ElementRendererBundle\Configurator\Configuration;
use Phlexible\Bundle\ElementRendererBundle\ElementRendererEvents;
use Phlexible\Bundle\ElementRendererBundle\Event\ConfigureEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
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
