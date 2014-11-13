<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
