<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\RenderConfigurator;

use Phlexible\Bundle\AccessControlBundle\Rights as ContentRightsManager;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementRendererBundle\RenderConfiguration;
use Phlexible\Bundle\TeaserBundle\Teaser\Teaser;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     * @param ElementService           $elementService
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        ElementService $elementService,
        SecurityContextInterface $securityContext)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->elementService = $elementService;
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, RenderConfiguration $renderConfiguration)
    {
        return;
        if (!($parameters instanceof Teaser)) {
            return;
        }

        // Before Init Element Event
        /*
        $beforeEvent = new \Makeweb_Renderers_Event_BeforeInitElement($this);
        if (!$this->dispatcher->dispatch($beforeEvent))
        {
            return false;
        }
        */

        /* @var $parameters Teaser */
        $teaser = $parameters;

        $element = $this->elementService->findElement($teaser->getTypeId());
        $version = $this->elementService->findLatestElementVersion($element)->getVersion();

        $renderConfiguration
            ->addFeature('teaser')
            ->set('teaser', $teaser)
            ->addFeature('eid')
            ->set('eid', $teaser->getTypeId())
            ->set('version', $version)
            ->set('language', 'de');

        // Init Element Event
        /*
        $event = new \Makeweb_Renderers_Event_InitElement($this);
        $this->dispatcher->dispatch($event);
        */
    }
}
