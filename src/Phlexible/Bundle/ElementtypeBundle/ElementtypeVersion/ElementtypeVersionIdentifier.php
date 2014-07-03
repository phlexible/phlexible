<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion;

use Phlexible\Bundle\LockBundle\LockIdentifier;

/**
 * Elementtype version identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeVersionIdentifier extends LockIdentifier
{
    /**
     * @param int    $elementTypeId
     * @param string $version
     */
    public function __construct($elementTypeId, $version)
    {
        parent::__construct($elementTypeId, $version);
    }
}
