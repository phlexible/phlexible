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
     * Fired after configuring contentchannel
     */
    const CONFIGURE_CONTENTCHANNEL = 'phlexible_element_renderer.configure_contentchannel';

    /**
     * Fired after configuring element
     */
    const CONFIGURE_ELEMENT = 'phlexible_element_renderer.configure_element';

    /**
     * Fired after configuring layoutarea
     */
    const CONFIGURE_LAYOUTAREA = 'phlexible_element_renderer.configure_layoutarea';

    /**
     * Fired after configuring navigation
     */
    const CONFIGURE_NAVIGATION = 'phlexible_element_renderer.configure_navigation';

    /**
     * Fired after configuring teaser
     */
    const CONFIGURE_TEASER = 'phlexible_element_renderer.configure_teaser';

    /**
     * Fired after configuring template
     */
    const CONFIGURE_TEMPLATE = 'phlexible_element_renderer.configure_template';

    /**
     * Fired after configuring tree node
     */
    const CONFIGURE_TREE_NODE = 'phlexible_element_renderer.configure_tree_node';

    /**
     * Fired after providing data
     */
    const PROVIDE = 'phlexible_element_renderer.provide';

    /**
     * Fired after rendering
     */
    const RENDER = 'phlexible_element_renderer.render';
}
