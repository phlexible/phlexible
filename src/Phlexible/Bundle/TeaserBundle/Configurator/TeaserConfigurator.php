<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Configurator;

use Phlexible\Bundle\AccessControlBundle\Rights as ContentRightsManager;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementRendererBundle\Configurator\ConfiguratorInterface;
use Phlexible\Bundle\ElementRendererBundle\Configurator\Configuration;
use Phlexible\Bundle\ElementRendererBundle\ElementRendererEvents;
use Phlexible\Bundle\ElementRendererBundle\Event\ConfigureEvent;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Teaser configurator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserConfigurator implements ConfiguratorInterface
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
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     * @param ElementService           $elementService
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        ElementService $elementService)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->elementService = $elementService;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, Configuration $renderConfiguration)
    {
        if (!$request->attributes->has('contentDocument') || !$request->attributes->get('contentDocument') instanceof Teaser) {
            return;
        }

        $teaser = $request->attributes->get('contentDocument');

        $element = $this->elementService->findElement($teaser->getTypeId());
        $version = $this->elementService->findLatestElementVersion($element)->getVersion();

        $renderConfiguration
            ->addFeature('teaser')
            ->setVariable('teaser', $teaser)
            ->addFeature('eid')
            ->set('eid', $teaser->getTypeId())
            ->set('version', $version)
            ->set('language', 'de');

        $event = new ConfigureEvent($renderConfiguration);
        $this->dispatcher->dispatch(ElementRendererEvents::CONFIGURE_TEASER, $event);
    }
}
