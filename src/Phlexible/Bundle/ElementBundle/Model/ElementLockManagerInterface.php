<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Model;

use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementLock;
use Phlexible\Bundle\ElementBundle\Exception\LockFailedException;

/**
 * Lock manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 */
interface ElementLockManagerInterface
{
    /**
     * Is element locked by either master or slave?
     *
     * @param Element $element
     * @param string  $language
     *
     * @return bool
     */
    public function isLocked(Element $element, $language);

    /**
     * @param Element $element
     *
     * @return bool
     */
    public function isMasterLocked(Element $element);

    /**
     * @param Element $element
     * @param string  $language
     *
     * @return bool
     */
    public function isSlaveLocked(Element $element, $language);

    /**
     * @param Element $element
     * @param string  $language
     * @param string  $userId
     *
     * @return bool
     */
    public function isLockedByUser(Element $element, $language, $userId);

    /**
     * @param Element $element
     * @param string  $userId
     *
     * @return bool
     */
    public function isMasterLockedByUser(Element $element, $userId);

    /**
     * @param Element $element
     * @param string  $language
     * @param string  $userId
     *
     * @return bool
     */
    public function isSlaveLockedByUser(Element $element, $language, $userId);

    /**
     * @param Element $element
     * @param string  $language
     * @param string  $userId
     *
     * @return bool
     */
    public function isLockedByOtherUser(Element $element, $language, $userId);

    /**
     * @param Element $element
     * @param string  $userId
     *
     * @return bool
     */
    public function isMasterLockedByOtherUser(Element $element, $userId);

    /**
     * @param Element $element
     * @param string  $language
     * @param string  $userId
     *
     * @return bool
     */
    public function isSlaveLockedByOtherUser(Element $element, $language, $userId);

    /**
     * @param Element $element
     * @param string  $userId
     * @param string  $language
     * @param string  $type
     *
     * @return ElementLock
     * @throws LockFailedException
     */
    public function lock(Element $element, $userId, $language = null, $type = ElementLock::TYPE_TEMPORARY);

    /**
     * @param Element $element
     * @param string  $language
     */
    public function unlock(Element $element, $language = null);

    /**
     * @param string $id
     *
     * @return ElementLock
     */
    public function find($id);

    /**
     * @param Element $element
     *
     * @return ElementLock|null
     */
    public function findMasterLock(Element $element);

    /**
     * @param Element $element
     * @param string  $language
     *
     * @return ElementLock|null
     */
    public function findSlaveLock(Element $element, $language);

    /**
     * @return ElementLock[]
     */
    public function findAll();

    /**
     * @param array    $criteria
     * @param array    $sort
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return ElementLock[]
     */
    public function findBy(array $criteria, array $sort = array(), $limit = null, $offset = null);

    /**
     * @param ElementLock $lock
     */
    public function deleteLock(ElementLock $lock);
}
