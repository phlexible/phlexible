<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\Event;

use Phlexible\Bundle\ElementRendererBundle\Configurator\RenderConfiguration;
use Symfony\Component\EventDispatcher\Event;

/**
 * Configure event
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class ConfigureEvent extends Event
{
    /**
     * @var \Phlexible\Bundle\ElementRendererBundle\Configurator\RenderConfiguration
     */
    private $configuration;

    /**
     * @param \Phlexible\Bundle\ElementRendererBundle\Configurator\RenderConfiguration $configuration
     */
    public function __construct(RenderConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return \Phlexible\Bundle\ElementRendererBundle\Configurator\RenderConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}