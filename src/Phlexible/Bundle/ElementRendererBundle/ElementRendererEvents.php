<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle;

/**
 * Element renderer events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementRendererEvents
{
    /**
     * Fired after providing data
     */
    const PROVIDE = 'phlexible_element_renderer.provide';

    /**
     * Fired after rendering
     */
    const RENDER = 'phlexible_element_renderer.render';
}