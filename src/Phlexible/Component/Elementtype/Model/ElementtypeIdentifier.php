<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\Model;

use Phlexible\Component\Identifier\Identifier;

/**
 * Elementtype identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeIdentifier extends Identifier
{
    /**
     * @param int $elementTypeId
     */
    public function __construct($elementTypeId)
    {
        parent::__construct($elementTypeId);
    }
}
