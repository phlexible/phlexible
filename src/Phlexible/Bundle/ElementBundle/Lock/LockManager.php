<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Lock;

use Phlexible\Bundle\ElementBundle\Element\Element;
use Phlexible\Bundle\ElementBundle\Element\ElementIdentifier;
use Phlexible\Bundle\LockBundle\Entity\Lock;
use Phlexible\Bundle\LockBundle\Lock\LockIdentifierInterface;
use Phlexible\Bundle\LockBundle\Lock\LockIdentityInterface;
use Phlexible\Bundle\LockBundle\Lock\LockManager as BaseLockManager;

/**
 * Elements locks service
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 */
class LockManager extends BaseLockManager
{
    /**
     * Is item locked?
     *
     * @param string|LockIdentifierInterface $identifier
     * @param bool                           $isSlave
     *
     * @return bool
     */
    public function isLockedPart($identifier, $isSlave)
    {
        if ($identifier instanceof LockIdentityInterface) {
            $identifier = $identifier->__toString();
        }

        try {
            if ($isSlave) {
                $locks = $this->repository->find($identifier);
            } else {
                $locks = $this->repository->findByIdentifierPart($identifier);
            }

            return count($locks) ? true : false;
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * Is item locked by user?
     *
     * @param string $identifier
     * @param bool   $isSlave
     * @param string $userId
     *
     * @return bool
     */
    public function isLockedPartByUser($identifier, $isSlave, $userId)
    {
        if ($identifier instanceof LockIdentityInterface) {
            $identifier = $identifier->__toString();
        }

        try {
            if ($isSlave) {
                $lock = $this->repository->findOneBy(array('id' => $identifier, 'user_id' => $userId));
            } else {
                $lock = $this->repository->findByIdentifierPartAndUserId($identifier, $userId);
            }

            return !empty($lock);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Is item locked by another user?
     *
     * @param string $identifier
     * @param bool   $isSlave
     * @param string $uid
     *
     * @return bool
     */
    public function isLockedPartByOtherUser($identifier, $isSlave, $uid)
    {
        if ($identifier instanceof LockIdentityInterface) {
            $identifier = $identifier->__toString();
        }

        try {
            if ($isSlave) {
                $lock = $this->repository->findByIdentifierAndNotUserId($identifier, $uid);
            } else {
                $lock = $this->repository->findByIdentifierPartAndOtherUserId($identifier, $uid);
            }

            return count($lock);
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * Is item locked by user?
     *
     * @param string|LockIdentifierInterface $identifier
     *
     * @return Lock
     */
    public function getLockPart($identifier)
    {
        if ($identifier instanceof LockIdentityInterface) {
            $identifier = $identifier->__toString();
        }

        try {
            $locks = $this->repository->findByIdentifierPart($identifier);

            if (!count($locks)) {
                return null;
            }

            return current($locks);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if element is locked.
     *
     * @param Element $element
     * @param string  $language (Optional) if language is null master language is used
     *
     * @return bool
     */
    public function isElementLocked(Element $element, $language = null)
    {
        $eid                  = $element->getEid();
        $masterLanguage       = $element->getMasterLanguage();
        $lockIdentifierMaster = new ElementIdentifier($eid);

        if ($masterLanguage === $language || null === $language) {
            // for master language check master and all slave languages
            $result = $this->isLockedPart($lockIdentifierMaster, false);
        } else {
            // for master language check master and slave language
            $lockIdentifierSlave = new ElementIdentifier($eid, $language);

            $result = $this->isLocked($lockIdentifierSlave)
                || $this->isLocked($lockIdentifierMaster);
        }

        return $result;
    }

    /**
     * Lock an element.
     *
     * @param Element $element
     * @param string  $uid
     * @param string  $language (Optional) if language is null master language is used
     */
    public function lockElement(Element $element, $uid, $language = null)
    {
        $eid            = $element->getEid();
        $masterLanguage = $element->getMasterLanguage();

        // use master or slave lock
        $lockIdentifier = ($language === $masterLanguage)
            ? new ElementIdentifier($eid)
            : new ElementIdentifier($eid, $language);

        // do locking
        $this->lock(
            $lockIdentifier,
            $uid,
            Lock::TYPE_TEMPORARY,
            'element',
            $eid
        );
    }

    /**
     * Unlock an element.
     *
     * @param Element $element
     * @param string  $uid
     * @param string  $language (Optional) if language is null master language is used
     */
    public function unlockElement(Element $element, $uid, $language = null)
    {
        $eid            = $element->getEid();
        $masterLanguage = $element->getMasterLanguage();

        // use master or slave lock
        $lockIdentifier = ($language === $masterLanguage)
            ? new LockIdentifierInterface($eid)
            : new ElementIdentifier($eid, $language);

        // do locking
        $this->unlock(
            $lockIdentifier,
            $uid
        );
    }
}
