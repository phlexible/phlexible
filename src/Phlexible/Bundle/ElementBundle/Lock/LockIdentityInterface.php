<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Lock;

/**
 * Lock identity
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LockIdentityInterface
{
    /**
     * Return string representation of this lock identity
     *
     * @return string
     */
    public function __toString();
}
