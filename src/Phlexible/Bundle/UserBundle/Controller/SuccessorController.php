<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Successor controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/users/users")
 * @Security("is_granted('users')")
 */
class SuccessorController extends Controller
{
    /**
     * @param string $userId
     *
     * @return JsonResponse
     * @Route("/{userId}/successors", name="users_successor_list")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="List applicable successor users"
     * )
     */
    public function listAction($userId)
    {
        $userManager = $this->get('phlexible_user.user_manager');
        $systemUid = $userManager->getSystemUserId();

        $skipUserIds = array($userId);
        if (!in_array($systemUid, $skipUserIds)) {
            $skipUserIds[] = $systemUid;
        }

        $users = array();
        foreach ($userManager->findAll() as $user) {
            if (in_array($user->getId(), $skipUserIds)) {
                continue;
            }

            $users[$user->getDisplayName()] = array(
                'uid'  => $user->getId(),
                'name' => $user->getDisplayName()
            );
        }

        ksort($users);
        $users = array_values($users);

        return new JsonResponse($users);
    }

    /**
     * @param Request $request
     * @param string  $userId
     *
     * @return ResultResponse
     * @Route("/{userId}/setsuccessor", name="users_successor_set")
     * @Method("POST")
     * @ApiDoc(
     *   description="Set successor",
     *   requirements={
     *     {"name"="successorUserId", "dataType"="string", "required"=true, "description"="Successor user ID"},
     *   }
     * )
     */
    public function setAction(Request $request, $userId)
    {
        $successorUserId = $request->get('successorUserId');

        $userManager = $this->get('phlexible_user.user_manager');
        $user = $userManager->find($userId);
        $successorUser = $userManager->find($successorUserId);

        $successor = $this->get('users.successor_service');
        $successor->set($user, $successorUser);

        return new ResultResponse(true, 'Successor set');
    }
}
