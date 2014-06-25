<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\RenderConfigurator;

use Phlexible\Bundle\ElementRendererBundle\RenderConfiguration;
use Symfony\Component\HttpFoundation\Request;

/**
 * Configurator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ConfiguratorInterface
{
    /**
     * @param Request             $renderRequest
     * @param RenderConfiguration $renderConfiguration
     */
    public function configure(Request $renderRequest, RenderConfiguration $renderConfiguration);
}