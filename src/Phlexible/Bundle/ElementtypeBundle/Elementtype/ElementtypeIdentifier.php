<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Elementtype;

use Phlexible\Bundle\LockBundle\LockIdentifier;

/**
 * Elementtype identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeIdentifier extends LockIdentifier
{
    /**
     * @param int $elementTypeId
     */
    public function __construct($elementTypeId)
    {
        parent::__construct($elementTypeId);
    }
}
