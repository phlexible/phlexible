<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure;

use Phlexible\Component\Identifier\Identifier;

/**
 * Elementtype structure identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeStructureIdentifier extends Identifier
{
    /**
     * @param integer $elementTypeId
     * @param string  $version
     */
    public function __construct($elementTypeId, $version)
    {
        parent::__construct($elementTypeId, $version);
    }
}
