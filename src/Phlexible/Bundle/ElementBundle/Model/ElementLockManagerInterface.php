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
     *
     * @return bool
     */
    public function isLocked(Element $element);

    /**
     * @param Element $element
     * @param string  $userId
     *
     * @return bool
     */
    public function isLockedByUser(Element $element, $userId);

    /**
     * @param Element $element
     * @param string  $userId
     *
     * @return bool
     */
    public function isLockedByOtherUser(Element $element, $userId);

    /**
     * @param Element $element
     * @param string  $userId
     * @param string  $type
     *
     * @return ElementLock
     */
    public function lock(Element $element, $userId, $type = ElementLock::TYPE_TEMPORARY);

    /**
     * @param Element $element
     */
    public function unlock(Element $element);

    /**
     * @param string $id
     *
     * @return ElementLock
     */
    public function find($id);

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
    public function findBy(array $criteria, array $sort = [], $limit = null, $offset = null);

    /**
     * @param ElementLock $lock
     */
    public function deleteLock(ElementLock $lock);

    public function deleteAll();
}
