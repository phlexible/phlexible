<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle;

/**
 * Elements events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementEvents
{
    /**
     * Fired before an element is created.
     */
    const BEFORE_CREATE_ELEMENT = 'phlexible_element.before_create_element';

    /**
     * Fired after an element is created.
     */
    const CREATE_ELEMENT = 'phlexible_element.create_element';

    /**
     * Fired before an element version is created.
     */
    const BEFORE_CREATE_ELEMENT_VERSION = 'phlexible_element.before_create_element_version';

    /**
     * Fired after an element version is created.
     */
    const CREATE_ELEMENT_VERSION = 'phlexible_element.create_element_version';

    /**
     * Fired before element is saved.
     */
    const BEFORE_SAVE_ELEMENT = 'phlexible_element.before_save_element';

    /**
     * Fired after element is saved.
     */
    const SAVE_ELEMENT = 'phlexible_element.save_element';

    /**
     * Fired before element data is saved.
     */
    const BEFORE_SAVE_ELEMENT_DATA = 'phlexible_element.before_save_element_data';

    /**
     * Fired after element data is saved.
     */
    const SAVE_ELEMENT_DATA = 'phlexible_element.save_element_data';

    /**
     * Fired before a tree node is updated.
     */
    const BEFORE_SAVE_NODE_DATA = 'phlexible_element.before_save_node_data';

    /**
     * Fired after a tree node has been saved.
     */
    const SAVE_NODE_DATA = 'phlexible_element.save_node_data';

    /**
     * Fired after a tree node has been loaded.
     */
    const LOAD_DATA = 'phlexible_element.load_data';

}
