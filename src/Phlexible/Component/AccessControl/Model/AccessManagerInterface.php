<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\Model;

use Phlexible\Component\AccessControl\Domain\AccessControlList;

/**
 * Access manager interface.
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
