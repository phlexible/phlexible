<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle;

/**
 * Elementtype events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ElementtypeEvents
{
    /**
     * Before Create Event
     * Fired before an elementtype is created
     */
    const BEFORE_CREATE = 'phlexible_elementtype.before_create';

    /**
     * Create Event
     * Fired after an elementtype is created
     */
    const CREATE = 'phlexible_elementtype.create';

    /**
     * Before Update Event
     * Fired before an elementtype is created
     */
    const BEFORE_UPDATE = 'phlexible_elementtype.before_update';

    /**
     * Update Event
     * Fired after an elementtype is created
     */
    const UPDATE = 'phlexible_elementtype.create';

    /**
     * Before Delete Event
     * Fired before an elementtype is deleted
     */
    const BEFORE_DELETE = 'phlexible_elementtype.before_delete';

    /**
     * Delete Event
     * Fired after an elementtype is deleted
     */
    const DELETE = 'phlexible_elementtype.delete';

    /**
     * Before Version Create Event
     * Fired before an elementtype version is created
     */
    const BEFORE_VERSION_CREATE = 'phlexible_elementtype.before_version_create';

    /**
     * Version Create Event
     * Fired after an elementtype version is created
     */
    const VERSION_CREATE = 'phlexible_elementtype.version_create';

    /**
     * Before Structure Create Event
     * Fired before an elementtype version is created
     */
    const BEFORE_STRUCTURE_CREATE = 'phlexible_elementtype.before_structure_create';

    /**
     * Structure Create Event
     * Fired after an elementtype version is created
     */
    const STRUCTURE_CREATE = 'phlexible_elementtype.structure_create';

    /**
     * Usage Event
     * Fired to collect usage
     */
    const USAGE = 'phlexible_elementtype.usage';
}
