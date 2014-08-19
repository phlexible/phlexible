<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Lock;

use Phlexible\Bundle\LockBundle\Lock\LockIdentifier;

/**
 * Element part lock identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementPartLockIdentifier extends LockIdentifier
{
    /**
     * @param int $eid
     */
    public function __construct($eid)
    {
        parent::__construct($eid);
    }
}
