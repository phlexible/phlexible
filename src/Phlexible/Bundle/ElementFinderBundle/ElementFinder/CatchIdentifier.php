<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder;

use Phlexible\Component\Identifier\Identifier;

/**
 * Catch identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CatchIdentifier extends Identifier
{
    /**
     * @param string $id
     */
    public function __construct($id)
    {
        parent::__construct("teasers_rotation_position_" . $id);
    }
}
