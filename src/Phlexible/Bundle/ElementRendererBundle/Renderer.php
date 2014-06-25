<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle;

use Phlexible\Bundle\ElementRendererBundle\Event\RenderEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element renderer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Renderer implements RendererInterface
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param RendererInterface        $renderer
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(RendererInterface $renderer, EventDispatcherInterface $dispatcher)
    {
        $this->renderer = $renderer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function render(RenderConfiguration $renderConfiguration)
    {
        $content = $this->renderer->render($renderConfiguration);

        $event = new RenderEvent($this, $renderConfiguration, $content);
        $this->dispatcher->dispatch(ElementRendererEvents::RENDER, $event);

        return $event->getContent();
    }

    public function getRenderer()
    {
        return $this->renderer;
    }
}