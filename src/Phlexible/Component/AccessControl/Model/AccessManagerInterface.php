<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Model;

use Phlexible\Component\AccessControl\Domain\AccessControlList;

/**
 * Access manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface AccessManagerInterface
{
    /**
     * @param ObjectIdentityInterface $objectIdentity
     *
     * @return AccessControlList
     */
    public function findAcl(ObjectIdentityInterface $objectIdentity);

    /**
     * @param AccessControlList $acl
     *
     * @return $this
     */
    public function updateAcl(AccessControlList $acl);

    /**
     * @param ObjectIdentityInterface $objectIdentity
     */
    public function deleteAcl(ObjectIdentityInterface $objectIdentity);
}
