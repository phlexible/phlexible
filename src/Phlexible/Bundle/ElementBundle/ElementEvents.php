<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle;

/**
 * Element events.
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
     * Fired before an element is deleted.
     */
    const BEFORE_DELETE_ELEMENT = 'phlexible_element.before_delete_element';

    /**
     * Fired after an element is deleted.
     */
    const DELETE_ELEMENT = 'phlexible_element.delete_element';

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
     * Fired before an element structure is created.
     */
    const BEFORE_CREATE_ELEMENT_STRUCTURE = 'phlexible_element.before_create_element_structure';

    /**
     * Fired after an element structure is created.
     */
    const CREATE_ELEMENT_STRUCTURE = 'phlexible_element.create_element_structure';

    /**
     * Fired before an element source is created.
     */
    const BEFORE_CREATE_ELEMENT_SOURCE = 'phlexible_element.before_create_element_source';

    /**
     * Fired after an element source is created.
     */
    const CREATE_ELEMENT_SOURCE = 'phlexible_element.create_element_source';

    /**
     * Fired before an element source is updated.
     */
    const BEFORE_UPDATE_ELEMENT_SOURCE = 'phlexible_element.before_update_element_source';

    /**
     * Fired after an element source has been updated.
     */
    const UPDATE_ELEMENT_SOURCE = 'phlexible_element.before_create_element_source';

    /**
     * Fired before element is saved.
     */
    const BEFORE_SAVE_ELEMENT = 'phlexible_element.before_save_element';

    /**
     * Fired after element is saved.
     */
    const SAVE_ELEMENT = 'phlexible_element.save_element';

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

    /**
     * Fired on element:changes --commit.
     */
    const COMMIT_CHANGES = 'phlexible_element.commit_changes';

    /**
     * Fired before an element is created.
     */
    const BEFORE_CREATE_ELEMENT_LINK = 'phlexible_element.before_create_element_link';

    /**
     * Fired after an element is created.
     */
    const CREATE_ELEMENT_LINK = 'phlexible_element.create_element_link';

    /**
     * Fired before an element is updated.
     */
    const BEFORE_UPDATE_ELEMENT_LINK = 'phlexible_element.before_update_element_link';

    /**
     * Fired after an element is updated.
     */
    const UPDATE_ELEMENT_LINK = 'phlexible_element.update_element_link';

    /**
     * Fired before an element is deleted.
     */
    const BEFORE_DELETE_ELEMENT_LINK = 'phlexible_element.before_delete_element_link';

    /**
     * Fired after an element is deleted.
     */
    const DELETE_ELEMENT_LINK = 'phlexible_element.delete_element_link';
}
