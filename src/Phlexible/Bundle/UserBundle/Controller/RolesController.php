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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Roles controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/users/roles")
 * @Security("is_granted('ROLE_USERS')")
 */
class RolesController extends Controller
{
    /**
     * List roles
     *
     * @return JsonResponse
     * @Route("", name="users_roles_list")
     * @Method("GET")
     * @ApiDoc(
     *   description="Returns a list of roles"
     * )
     */
    public function listAction()
    {
        $roleHierarchy = $this->container->getParameter('security.role_hierarchy.roles');

        $roles = [];
        foreach (array_keys($roleHierarchy) as $role) {
            $roles[] = ['id' => $role, 'name' => $role];
        }

        return new JsonResponse($roles);
    }
}
