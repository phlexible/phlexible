<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle;

/**
 * Renderer interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface RendererInterface
{
    /**
     * @param RenderConfiguration $renderConfiguration
     * @return string
     */
    public function render(RenderConfiguration $renderConfiguration);
}