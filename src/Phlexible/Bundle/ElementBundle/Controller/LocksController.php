<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Phlexible\Bundle\ElementBundle\Entity\ElementLock;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Locks controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/locks")
 * @Security("is_granted('ROLE_ELEMENTS')")
 */
class LocksController extends Controller
{
    /**
     * List locks
     *
     * @return JsonResponse
     * @Route("/list", name="locks_list")
     */
    public function listAction()
    {
        $lockManager = $this->get('phlexible_element.element_lock_manager');
        $userManager = $this->get('phlexible_user.user_manager');

        $locks = $lockManager->findAll();

        $data = [];
        foreach ($locks as $lock) {
            /* @var $lock ElementLock */
            $username = '(unknown user)';
            $user = $userManager->find($lock->getUserId());
            if ($user) {
                $username = $user->getDisplayName();
            }

            $data[] = [
                'id'        => $lock->getId(),
                'uid'       => $lock->getUserId(),
                'user'      => $username,
                'ts'        => $lock->getLockedAt()->format('Y-m-d H:i:s'),
                'eid'       => $lock->getElement()->getEid(),
                'lock_type' => $lock->getType(),
            ];
        }

        return new JsonResponse(['locks' => $data]);
    }

    /**
     * Delete lock
     *
     * @param string $id
     *
     * @return ResultResponse
     * @Route("/delete/{id}", name="locks_delete")
     */
    public function deleteAction($id)
    {
        $lockManager = $this->get('phlexible_element.element_lock_manager');
        $lock = $lockManager->find($id);

        $lockManager->deleteLock($lock);

        return new ResultResponse(true, 'Lock released.');
    }

    /**
     * Delete my locks
     *
     * @return ResultResponse
     * @Route("/deletemy", name="locks_delete_my")
     */
    public function deletemyAction()
    {
        $uid = $this->getUser()->getId();

        $lockManager = $this->get('phlexible_element.element_lock_manager');
        $myLocks = $lockManager->findBy(['userId' => $uid]);

        foreach ($myLocks as $lock) {
            $lockManager->deleteLock($lock);
        }

        return new ResultResponse(true, 'My locks released.');

    }

    /**
     * Delete all locks
     *
     * @return ResultResponse
     * @Route("/flush", name="locks_flush")
     */
    public function flushAction()
    {
        $lockManager = $this->get('phlexible_element.element_lock_manager');

        $lockManager->deleteAll();

        return new ResultResponse(true, 'All locks released.');
    }

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

        $elementService = $this->get('phlexible_element.element_service');
        $lockManager = $this->get('phlexible_element.element_lock_manager');

        $element = $elementService->findElement($eid);

        if ($lockManager->isLocked($element, $language)) {
            return new ResultResponse(false, 'Element already locked.');
        } else {
            try {
                // try to lock the element
                $lockManager->lock($element, $this->getUser()->getId(), $language);

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
        $language = $request->get('language');

        $elementService = $this->get('phlexible_element.element_service');
        $lockManager = $this->get('phlexible_element.element_lock_manager');

        $element = $elementService->findElement($unlockId);
        $userId = $this->getUser()->getId();

        if (!$force && !$lockManager->isLockedByUser($element, $language, $userId)) {
            return new ResultResponse(false, 'Not locked by you.');
        } elseif (!$lockManager->isLocked($element, $language)) {
            return new ResultResponse(false, 'Not locked.');
        } else {
            try {
                $lockManager->unlock($element, $language);

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
        $elementService = $this->get('phlexible_element.element_service');
        $lockManager = $this->get('phlexible_element.element_lock_manager');

        // get element data object
        $element = $elementService->findElement($eid);

        // get user information for deletion
        $userId = $this->getUser()->getId();

        if ($element->getMasterLanguage() === $language) {
            // if master language should be unlocked, all slave languages must be unlocked too
            $lockManager->unlock($element);
        } else {
            // if slave language should be unlocked, only master language must be unlocked too
            $lockManager->unlock($element, $language);
            $lockManager->unlock($element);
        }

        return new ResultResponse(true, 'Lock removed.');
    }
}
