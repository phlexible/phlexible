<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion;

use Phlexible\Component\Identifier\Identifier;

/**
 * Element version identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementVersionIdentifier extends Identifier
{
    /**
     * @param integer $eid
     * @param integer $version
     */
    public function __construct($eid, $version)
    {
        parent::__construct($eid, $version);
    }
}
