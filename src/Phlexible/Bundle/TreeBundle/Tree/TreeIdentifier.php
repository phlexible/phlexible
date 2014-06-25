<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Phlexible\Component\Identifier\Identifier;

/**
 * Tree identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeIdentifier extends Identifier
{
    /**
     * @param string $siterootId
     */
    public function __construct($siterootId)
    {
        parent::__construct($siterootId);
    }
}
