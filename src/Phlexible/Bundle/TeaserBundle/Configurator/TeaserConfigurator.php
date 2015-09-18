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
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
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
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     * @param TeaserManagerInterface  $teaserManager
     * @param ElementService           $elementService
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        TeaserManagerInterface $teaserManager,
        ElementService $elementService)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->teaserManager = $teaserManager;
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

        $version = -1;
        if (!$request->attributes->get('_preview')) {
            $version = $this->teaserManager->getPublishedVersion($teaser, $request->getLocale());

            if (!$version) {
                throw new \Exception("Teaser not published.");
            }
        }

        $renderConfiguration
            ->addFeature('teaser')
            ->setVariable('teaser', $teaser)
            ->addFeature('eid')
            ->set('eid', $teaser->getTypeId())
            ->set('version', $version)
            ->set('language', $request->getLocale());

        $event = new ConfigureEvent($renderConfiguration);
        $this->dispatcher->dispatch(ElementRendererEvents::CONFIGURE_TEASER, $event);
    }
}
