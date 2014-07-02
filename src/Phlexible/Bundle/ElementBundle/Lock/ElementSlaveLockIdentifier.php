<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Lock;

use Phlexible\Bundle\LockBundle\Lock\LockIdentifierInterface;

/**
 * Element identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementSlaveLockIdentifier extends LockIdentifierInterface
{
    /**
     * @param int    $eid
     * @param string $language
     */
    public function __construct($eid, $language)
    {
        parent::__construct($eid, 'slave', $language);
    }
}
