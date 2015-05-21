<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype;

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
     * Fired to collect usage
     */
    const USAGE = 'phlexible_elementtype.usage';
}
