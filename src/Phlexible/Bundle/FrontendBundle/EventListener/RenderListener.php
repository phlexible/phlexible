<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Render listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RenderListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onRender(Makeweb_Renderers_Event_Render $event)
    {
        if ($this->container->getParameter('profile.enabled')) {
            return;
        }

        $collection = $this->container->get('profiler.collectors');

        /* @var $renderer Makeweb_Renderers_Html */
        $renderer = $event->getRenderer();
        $view = $renderer->getView();
        $dwoo = $view->getEngine();

        $collection->getCollector('dwoo')
            ->setDwoo($dwoo)
            ->setTemplate($renderer->getTemplate());
    }
}