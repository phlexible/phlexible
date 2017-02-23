<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementRendererBundle;

/**
 * Element renderer events.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementRendererEvents
{
    /**
     * Fired after configuring contentchannel.
     */
    const CONFIGURE_CONTENTCHANNEL = 'phlexible_element_renderer.configure_contentchannel';

    /**
     * Fired after configuring element.
     */
    const CONFIGURE_ELEMENT = 'phlexible_element_renderer.configure_element';

    /**
     * Fired after configuring layoutarea.
     */
    const CONFIGURE_LAYOUTAREA = 'phlexible_element_renderer.configure_layoutarea';

    /**
     * Fired after configuring navigation.
     */
    const CONFIGURE_NAVIGATION = 'phlexible_element_renderer.configure_navigation';

    /**
     * Fired after configuring teaser.
     */
    const CONFIGURE_TEASER = 'phlexible_element_renderer.configure_teaser';

    /**
     * Fired after configuring template.
     */
    const CONFIGURE_TEMPLATE = 'phlexible_element_renderer.configure_template';

    /**
     * Fired after configuring tree node.
     */
    const CONFIGURE_TREE_NODE = 'phlexible_element_renderer.configure_tree_node';
}
