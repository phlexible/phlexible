<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Successor controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/users/successor")
 * @Security("is_granted('ROLE_USERS')")
 */
class SuccessorController extends Controller
{
    /**
     * @param string $userId
     *
     * @return JsonResponse
     * @Route("/list/{userId}", name="users_successor_list")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="List applicable successor users"
     * )
     */
    public function listAction($userId)
    {
        $userManager = $this->get('phlexible_user.user_manager');
        $systemUid = $userManager->getSystemUserId();

        $skipUserIds = [$userId];
        if (!in_array($systemUid, $skipUserIds)) {
            $skipUserIds[] = $systemUid;
        }

        $users = [];
        foreach ($userManager->findAll() as $user) {
            if (in_array($user->getId(), $skipUserIds)) {
                continue;
            }

            $users[$user->getDisplayName()] = [
                'uid' => $user->getId(),
                'name' => $user->getDisplayName(),
            ];
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
     * @Route("/set/{userId}", name="users_successor_set")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="Set successor",
     *   requirements={
     *     {"name"="successorUserId", "dataType"="string", "required"=true, "description"="Successor user ID"},
     *   }
     * )
     */
    public function setAction(Request $request, $userId)
    {
        $successorUserId = $request->get('successor');

        $userManager = $this->get('phlexible_user.user_manager');
        $user = $userManager->find($userId);
        $successorUser = $userManager->find($successorUserId);

        $successor = $this->get('phlexible_user.successor_service');
        $successor->set($user, $successorUser);

        return new ResultResponse(true, 'Successor set');
    }
}
