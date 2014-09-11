<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\Event;

use Phlexible\Bundle\ElementRendererBundle\Configurator\RenderConfiguration;
use Phlexible\Bundle\ElementRendererBundle\Renderer;
use Symfony\Component\EventDispatcher\Event;

/**
 * Render event
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class RenderEvent extends Event
{
    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var RenderConfiguration
     */
    private $configuration;

    /**
     * @var string
     */
    private $content;

    /**
     * @param Renderer            $renderer
     * @param \Phlexible\Bundle\ElementRendererBundle\Configurator\RenderConfiguration $configuration
     * @param string              $content
     */
    public function __construct(Renderer $renderer, RenderConfiguration $configuration, $content)
    {
        $this->renderer = $renderer;
        $this->configuration = $configuration;
        $this->content = $content;
    }

    /**
     * @return Renderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @return RenderConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}