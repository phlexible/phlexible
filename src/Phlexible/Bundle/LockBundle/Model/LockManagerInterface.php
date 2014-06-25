<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\LockBundle\Model;

use Phlexible\Bundle\LockBundle\Entity\Lock;
use Phlexible\Bundle\LockBundle\Exception\LockFailedException;
use Phlexible\Bundle\LockBundle\Lock\LockIdentityInterface;

/**
 * Lock manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 */
interface LockManagerInterface
{
    /**
     * Is item locked?
     *
     * @param LockIdentityInterface|string $identifier
     *
     * @return bool
     */
    public function isLocked($identifier);

    /**
     * Is item locked by user?
     *
     * @param LockIdentityInterface|string $identifier
     * @param string                       $userId
     *
     * @return bool
     */
    public function isLockedByUser($identifier, $userId);

    /**
     * Lock item
     *
     * @param LockIdentityInterface|string $identifier
     * @param string                       $userId
     * @param string                       $type
     * @param string                       $objectType
     * @param string                       $objectId
     *
     * @return Lock
     * @throws LockFailedException
     */
    public function lock($identifier, $userId, $type = Lock::TYPE_TEMPORARY,
                         $objectType = null, $objectId = null);

    /**
     * Remove lock from item
     *
     * @param LockIdentityInterface|string $identifier
     */
    public function unlock($identifier);

    /**
     * Find lock
     *
     * @param LockIdentityInterface|string $identifier
     *
     * @return Lock
     */
    public function find($identifier);

    /**
     * Find all locks
     *
     * @return Lock[]
     */
    public function findAll();

    /**
     * Find by criteria
     *
     * @param array    $criteria
     * @param array    $sort
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return Lock[]
     */
    public function findBy(array $criteria, array $sort = array(), $limit = null, $offset = null);

    /**
     * Delete lock
     *
     * @param Lock $lock
     */
    public function deleteLock(Lock $lock);
}
