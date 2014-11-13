<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\DataProvider;

use Phlexible\Bundle\ElementRendererBundle\Configurator\RenderConfiguration;

/**
 * Data provider interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DataProviderInterface
{
    /**
     * @param \Phlexible\Bundle\ElementRendererBundle\Configurator\RenderConfiguration $renderConfiguration
     *
     * @return \ArrayObject
     */
    public function provide(RenderConfiguration $renderConfiguration);
}
