<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\SecurityBundle\Acl\Acl;
use Phlexible\Bundle\SecurityBundle\Acl\AclMessage;
use Phlexible\Bundle\SecurityBundle\Entity\Resource;
use Phlexible\Bundle\SecurityBundle\Entity\Role;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * ACL controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/security/acl")
 * @Security("is_granted('roles')")
 */
class AclController extends Controller
{
    /**
     * Return roles
     *
     * @return JsonResponse
     * @Route("/roles", name="security_acl_roles")
     * @Method("GET")
     * @ApiDoc(
     *   description="Returns a list of acl roles"
     * )
     */
    public function rolesAction()
    {
        $acl = $this->get('phlexible_security.acl');
        $allRoles = $acl->getRoles();

        $roles = array();
        foreach ($allRoles as $role) {
            $item = array(
                'id'   => $role,
                'name' => $role,
                'type' => '',
            );

            if (in_array($role, array(Acl::ROLE_SUPERADMIN, Acl::ROLE_DEVELOPER, Acl::ROLE_ANONYMOUS))
                    && !$this->get('security.context')->isGranted(Acl::RESOURCE_DEBUG)) {
                continue;
            }

            if (in_array($role, array(Acl::ROLE_ANONYMOUS, Acl::ROLE_SUPERADMIN, Acl::ROLE_DEVELOPER))) {
                $item['type'] = 'locked';
            } elseif (in_array($role, array(Acl::ROLE_USER))) {
                $item['type'] = 'editable';
            }

            $roles[] = $item;
        }

        return new JsonResponse($roles);
    }

    /**
     * Return resources for a role
     *
     * @param string $role
     *
     * @return JsonResponse
     * @Route("/roles/{role}/resources", name="security_acl_role_resources")
     * @Method("GET")
     * @ApiDoc(
     *   description="Returns a list of acl resources"
     * )
     */
    public function roleResourcesAction($role)
    {
        $acl = $this->get('phlexible_security.acl');

        $resources = array();
        foreach ($acl->getResources() as $resource) {
            $resources[] = array(
                'id'      => $resource,
                'name'    => ucfirst(str_replace('_', ' ', $resource)),
                'allowed' => $acl->isAllowed($role, $resource)
            );
        }

        return new JsonResponse($resources);
    }

    /**
     * Return resources
     *
     * @return JsonResponse
     * @Route("/resources", name="security_acl_resources")
     * @Method("GET")
     * @ApiDoc(
     *   description="Returns a list of acl resources"
     * )
     */
    public function resourcesAction()
    {
        $acl = $this->get('phlexible_security.acl');

        $resources = array();
        foreach ($acl->getResources() as $resource) {
            $resources[] = array(
                'id'   => $resource,
                'name' => ucfirst(str_replace('_', ' ', $resource)),
            );
        }

        return new JsonResponse($resources);
    }

    /**
     * Create role
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/roles", name="security_acl_create")
     * @Method("POST")
     * @ApiDoc(
     *   description="Create role",
     *   requirements={
     *     {"name"="resources", "dataType"="string", "required"=false, "description"="Optional resources for new role"}
     *   }
     * )
     */
    public function createAction(Request $request)
    {
        $roleId = $request->get('role');

        $acl = $this->get('phlexible_security.acl');
        if ($acl->hasRole($roleId)) {
            return new ResultResponse(false, "Role $roleId already exists.");
        }

        $em = $this->getDoctrine()->getManager();

        $role = new Role();
        $role
            ->setRole($roleId)
            ->setCreateUserId($this->getUser()->getId())
            ->setCreatedAt(new \DateTime())
            ->setModifyUserId($this->getUser()->getId())
            ->setModifiedAt(new \DateTime())
        ;
        $em->persist($role);

        $resources = null;
        $body = null;
        if ($request->get('resources')) {
            $resources = explode(',', $request->get('resources'));

            $body = 'Resources:' . PHP_EOL . $request->get('resources');

            foreach ($resources as $resource) {
                if (!$acl->has($resource)) {
                    continue;
                }

                $resource = new Resource();
                $resource
                    ->setRole($role)
                    ->setResource($resource)
                    ->setOp('allow');
                ;
                $em->persist($resource);
            }
        }

        $em->flush();

        $this->get('phlexible_message.message_poster')
            ->post(AclMessage::create("Role $roleId created.", $body));

        return new ResultResponse(true, "Role $roleId created.");
    }

    /**
     * Rename role
     *
     * @param Request $request
     * @param string  $role
     *
     * @return ResultResponse
     * @Route("/roles/{role}", name="security_acl_rename")
     * @Method("PATCH")
     * @ApiDoc(
     *   description="Rename role",
     *   requirements={
     *     {"name"="name", "dataType"="string", "required"=true, "description"="New role name"}
     *   }
     * )
     */
    public function renameAction(Request $request, $role)
    {
        $roleId = $role;

        $acl = $this->get('phlexible_security.acl');
        if (!$acl->hasRole($role)) {
            return new ResultResponse(false, "Unknown role $roleId.");
        }

        $name = $request->get('name');

        if (!$name || $name === $roleId) {
            return new ResultResponse(true, "Role $roleId not changed.");
        }

        $roleRepository = $this->getDoctrine()->getRepository('PhlexibleSecurityBundle:Role');
        $em = $this->getDoctrine()->getManager();

        $role = $roleRepository->findOneByRole($roleId)
            ->setRole($name)
            ->setModifyUserId($this->getUser()->getId())
            ->setModifiedAt(new \DateTime());

        $em->flush($role);

        $this->get('phlexible_message.message_poster')
            ->post(AclMessage::create("Role $roleId renamed to $name."));

        return new ResultResponse(true, "Role $roleId renamed to $name.");
    }

    /**
     * @param string $role
     *
     * @return ResultResponse
     * @Route("/roles/{role}", name="security_acl_delete")
     * @Method("DELETE")
     * @ApiDoc(
     *   description="Delete role",
     *   parameters={
     *     {"name"="role", "dataType"="string", "required"=true, "description"="Role to delete"}
     *   }
     * )
     */
    public function deleteAction($role)
    {
        $roleId = $role;

        $acl = $this->get('phlexible_security.acl');
        if (!$acl->hasRole($role)) {
            return new ResultResponse(false, "Unknown role $role.");
        }

        $roleRepository = $this->getDoctrine()->getRepository('PhlexibleSecurityBundle:Role');
        $em = $this->getDoctrine()->getManager();

        $role = $roleRepository->findOneByRole($roleId);
        $em->remove($role);

        $em->flush();

        $this->get('phlexible_message.message_poster')
            ->post(AclMessage::create("Role $roleId deleted."));

        return new ResultResponse(true, "Role $roleId deleted.");
    }

    /**
     * @param Request $request
     * @param string  $role
     *
     * @return ResultResponse
     * @Route("/roles/{role}", name="security_acl_save")
     * @Method("PUT")
     * @ApiDoc(
     *   description="Save role",
     *   requirements={
     *     {"name"="resources", "dataType"="string", "required"=true, "description"="Resources for role"}
     *   }
     * )
     */
    public function saveAction(Request $request, $role)
    {
        $roleId = $role;

        $resources = $request->get('resources');
        $resources = explode(',', $resources);

        $acl = $this->get('phlexible_security.acl');
        if (!$acl->hasRole($roleId)) {
            return new ResultResponse(false, "Unknown role $role.");
        }

        $roleRepository = $this->getDoctrine()->getRepository('PhlexibleSecurityBundle:Role');
        $em = $this->getDoctrine()->getManager();

        $role = $roleRepository->findOneByRole($roleId);
        $oldResources = array();
        foreach ($role->getResources() as $resource) {
            $oldResources[] = $resource->getResource();
        }

        $added = array_diff($resources, $oldResources);
        $removed = array_diff($oldResources, $resources);
        $same = array_intersect($resources, $oldResources);

        if (!count($added) && !count($removed)) {
            return new ResultResponse(true, "Role $roleId not changed.");
        }

        $body = 'Changes:' . PHP_EOL
              . (count($added) ? '+ ' . implode(PHP_EOL . '+ ', $added) . PHP_EOL : '')
              . (count($added) ? '- ' . implode(PHP_EOL . '- ', $removed) . PHP_EOL : '')
              . '  ' . implode(PHP_EOL . '  ', $same);

        foreach ($added as $resourceName) {
            if (!$acl->has($resourceName)) {
                continue;
            }

            $resource = new Resource();
            $resource
                ->setResource($resourceName)
                ->setRole($role)
                ->setOp('allow');
            $em->persist($resource);
        }

        foreach ($removed as $resourceName) {
            foreach ($role->getResources() as $resource) {
                if ($resource->getResource() === $resourceName) {
                    $role->removeResource($resource);
                }
            }
        }

        $em->flush();

        $this->get('phlexible_message.message_poster')
            ->post(AclMessage::create("Role $roleId updated.", $body));

        return new ResultResponse(true, "Role $roleId updated.");
    }
}
