<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\LockBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Data controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/locks")
 * @Security("is_granted('locks')")
 */
class DataController extends Controller
{
    /**
     * List locks
     *
     * @return JsonResponse
     * @Route("/list", name="locks_list")
     */
    public function listAction()
    {
        $lockManager = $this->get('phlexible_lock.lock_manager');
        $userManager = $this->get('phlexible_user.user_manager');

        $locks = $lockManager->findAll();

        $data = array();
        foreach ($locks as $lock) {
            $username = '(unknown user)';
            $user = $userManager->find($lock->getUserId());
            if ($user) {
                $username = $user->getDisplayName();
            }

            $data[] = array(
                'id'          => $lock->getId(),
                'uid'         => $lock->getUserId(),
                'user'        => $username,
                'ts'          => $lock->getLockedAt()->format('Y-m-d H:i:s'),
                'lock_type'   => $lock->getType(),
                'object_type' => $lock->getObjectType(),
                'object_id'   => $lock->getObjectId(),
            );
        }

        return new JsonResponse(array('locks' => $data));
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
        $lockManager = $this->get('phlexible_lock.lock_manager');
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

        $lockManager = $this->get('phlexible_lock.lock_manager');
        $myLocks = $lockManager->findBy(array('userId' => $uid));

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
        $lockManager = $this->get('phlexible_lock.lock_manager');
        $locks = $lockManager->findAll();

        foreach ($locks as $lock) {
            $lockManager->deleteLock($lock);
        }

        return new ResultResponse(true, 'All locks released.');
    }
}