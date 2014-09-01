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
class ElementtypeEvents
{
    /**
     * Fired before an elementtype is created
     */
    const BEFORE_CREATE = 'phlexible_elementtype.before_create';

    /**
     * Fired after an elementtype is created
     */
    const CREATE = 'phlexible_elementtype.create';

    /**
     * Fired before an elementtype is created
     */
    const BEFORE_UPDATE = 'phlexible_elementtype.before_update';

    /**
     * Fired after an elementtype is created
     */
    const UPDATE = 'phlexible_elementtype.create';

    /**
     * Fired before an elementtype is deleted
     */
    const BEFORE_DELETE = 'phlexible_elementtype.before_delete';

    /**
     * Fired after an elementtype is deleted
     */
    const DELETE = 'phlexible_elementtype.delete';

    /**
     * Fired before an elementtype is deleted
     */
    const BEFORE_SOFT_DELETE = 'phlexible_elementtype.before_soft_delete';

    /**
     * Fired after an elementtype is deleted
     */
    const SOFT_DELETE = 'phlexible_elementtype.soft_delete';

    /**
     * Fired before an elementtype version is created
     */
    const BEFORE_VERSION_CREATE = 'phlexible_elementtype.before_version_create';

    /**
     * Fired after an elementtype version is created
     */
    const VERSION_CREATE = 'phlexible_elementtype.version_create';

    /**
     * Fired before an elementtype version is created
     */
    const BEFORE_STRUCTURE_CREATE = 'phlexible_elementtype.before_structure_create';

    /**
     * Fired after an elementtype version is created
     */
    const STRUCTURE_CREATE = 'phlexible_elementtype.structure_create';

    /**
     * Fired to collect usage
     */
    const USAGE = 'phlexible_elementtype.usage';
}
