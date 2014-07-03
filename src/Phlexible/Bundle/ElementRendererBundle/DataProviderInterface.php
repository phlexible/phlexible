<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle;

/**
 * Data provider interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DataProviderInterface
{
    /**
     * @param RenderConfiguration $renderConfiguration
     *
     * @return \ArrayObject
     */
    public function provide(RenderConfiguration $renderConfiguration);
}