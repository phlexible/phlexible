<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Phlexible\Bundle\ElementBundle\Lock\ElementMasterLockIdentifier;
use Phlexible\Bundle\ElementBundle\Lock\ElementSlaveLockIdentifier;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Locks controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/locks")
 * @Security("is_granted('elements')")
 */
class LocksController extends Controller
{
    /**
     * Lock element
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/lock", name="elements_locks_lock")
     */
    public function lockAction(Request $request)
    {
        // get request parameters
        $eid = (int) $request->get('eid');
        $language = $request->get('language');

        // get managers from container
        $elementService = $this->get('phlexible_element.service');
        $lockService = $this->get('phlexible_element.lock.service');

        // get element object
        $element = $elementService->findElement($eid);

        if ($lockService->isElementLocked($element, $language)) {
            return new ResultResponse(false, 'Element already locked.');
        } else {
            try {
                // try to lock the element
                $lockService->lockElement($element, $language);

                return new ResultResponse(true, 'Lock aquired.');
            } catch (\Exception $e) {
                return new ResultResponse(false, $e->getMessage());
            }
        }
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/unlock", name="elements_locks_unlock")
     */
    public function unlockAction(Request $request)
    {
        $unlockId = (int) $request->get('id');
        $force = $request->get('force', false);

        $lockService = $this->get('locks.service');
        $userId = $this->getUser()->getId();

        if (!$force && !$lockService->isLockedByUser($unlockId, $userId)) {
            return new ResultResponse(false, 'Not locked by you.');
        } elseif (!$lockService->isLocked($unlockId)) {
            return new ResultResponse(false, 'Not locked.');
        } else {
            try {
                $lockService->unlock($unlockId, $userId);

                return new ResultResponse(true, 'Lock removed.');
            } catch (\Exception $e) {
                return new ResultResponse(false, $e->getMessage());
            }
        }
    }

    /**
     * Force unlocking of an element, e.g. if element is not unlocked automatically.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/unlock/force", name="elements_locks_forceunlock")
     */
    public function forceunlockAction(Request $request)
    {
        // get request parameters
        $eid = (int) $request->get('eid');
        $language = $request->get('language');

        // get helpers from container
        $elementService = $this->get('phlexible_element.service');
        $lockRepository = $this->get('phlexible_lock.repository');
        $lockService = $this->get('phlexible_element.lock.service');

        // get element data object
        $element = $elementService->findElement($eid);

        // get user information for deletion
        $userId = $this->getUser()->getId();

        // get lock master/slave identifiers for current eid/language
        $lockIdentifierMaster = new ElementMasterLockIdentifier($eid);

        if ($element->getMasterLanguage() === $language) {
            // if master language should be unlocked, all slave languages must be unlocked too
            $lockIdentifiers = $lockRepository->findByIdentifierPart($lockIdentifierMaster);
        } else {
            // if slave language should be unlocked, only master language must be unlocked too
            $lockIdentifierSlave = new ElementSlaveLockIdentifier($eid, $language);
            $lockIdentifiers = array($lockIdentifierMaster, $lockIdentifierSlave);
        }

        foreach ($lockIdentifiers as $lockIdentifier) {
            try {
                $lockService->unlock($lockIdentifier, $userId);
            } catch (\Exception $e) {
            }
        }

        return new ResultResponse(true, 'Lock removed.');
    }
}
