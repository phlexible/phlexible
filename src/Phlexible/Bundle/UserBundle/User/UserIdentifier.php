<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\User;

use Phlexible\Component\Identifier\Identifier;

/**
 * User identifier
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class UserIdentifier extends Identifier
{
    /**
     * @param int $uid
     */
    public function __construct($uid)
    {
        parent::__construct($uid);
    }
}
