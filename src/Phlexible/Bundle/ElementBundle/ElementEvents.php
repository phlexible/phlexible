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
     * Fired before an element is updated.
     */
    const BEFORE_UPDATE_ELEMENT = 'phlexible_element.before_update_element';

    /**
     * Fired after an element is updated.
     */
    const UPDATE_ELEMENT = 'phlexible_element.update_element';

    /**
     * Fired before an element version is created.
     */
    const BEFORE_CREATE_ELEMENT_VERSION = 'phlexible_element.before_create_element_version';

    /**
     * Fired after an element version is created.
     */
    const CREATE_ELEMENT_VERSION = 'phlexible_element.create_element_version';

    /**
     * Fired before an element version is updated.
     */
    const BEFORE_UPDATE_ELEMENT_VERSION = 'phlexible_element.before_update_element_version';

    /**
     * Fired after an element version is updated.
     */
    const UPDATE_ELEMENT_VERSION = 'phlexible_element.update_element_version';

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
     * Fired on node data save.
     */
    const SAVE_NODE_DATA = 'phlexible_element.save_node_data';

    /**
     * Fired on teaser data save.
     */
    const SAVE_TEASER_DATA = 'phlexible_element.save_teaser_data';

    /**
     * Fired on element load.
     */
    const LOAD_DATA = 'phlexible_element.load_data';

}
