<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Siteroot;

use Phlexible\Component\Identifier\Identifier;

/**
 * Siteroot identifier
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class SiterootIdentifier extends Identifier
{
    /**
     * @param string $siterootId
     */
    public function __construct($siterootId)
    {
        parent::__construct($siterootId);
    }
}
