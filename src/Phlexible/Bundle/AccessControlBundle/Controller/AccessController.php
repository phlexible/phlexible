<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Component\AccessControl\Exception\InvalidArgumentException;
use Phlexible\Component\AccessControl\Model\ObjectIdentity;
use Phlexible\Component\AccessControl\Permission\PermissionResolver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Access controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/accesscontrol")
 * @Security("is_granted('ROLE_ACCESS_CONTROL')")
 */
class AccessController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/users", name="accesscontrol_users")
     */
    public function usersAction(Request $request)
    {
        $query  = $request->get('query');
        $limit  = $request->get('limit', 20);
        $offset = $request->get('start', 0);

        $userProvider = $this->get('phlexible_access_control.provider.user');

        $data = $userProvider->getAll($query, $limit, $offset);

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/groups", name="accesscontrol_groups")
     */
    public function groupsAction(Request $request)
    {
        $query  = $request->get('query');
        $limit  = $request->get('limit', 20);
        $offset = $request->get('start', 0);

        $userProvider = $this->get('phlexible_access_control.provider.group');

        $data = $userProvider->getAll($query, $limit, $offset);

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/permissions", name="accesscontrol_permissions")
     */
    public function permissionsAction(Request $request)
    {
        $objectType = $request->get('objectType');

        $permissionRegistry = $this->get('phlexible_access_control.permission_registry');
        $permissions = array();
        foreach ($permissionRegistry->get($objectType)->all() as $permission) {
            $permissions[] = [
                'name'    => $permission->getName(),
                'bit'     => $permission->getBit(),
                'iconCls' => 'null',
            ];
        }

        return new JsonResponse(array('permissions' => $permissions));
    }

    /**
     * List subjects
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/identities", name="accesscontrol_identities")
     */
    public function identitiesAction(Request $request)
    {
        $objectType = $request->get('objectType');
        $objectId = $request->get('objectId');

        $objectIdentity = new ObjectIdentity($objectId, $objectType);

        $accessManager = $this->get('phlexible_access_control.access_manager');
        $acl = $accessManager->findAcl($objectIdentity);
        $permissionResolver = $this->get('phlexible_access_control.permission_resolver');
        dump($acl);
        dump($permissionResolver->resolve($acl));
        die;

        return new JsonResponse($acl->getPermissions());
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @throws InvalidArgumentException
     * @Route("/save", name="accesscontrol_save")
     */
    public function saveAction(Request $request)
    {
        $rightType = $request->get('right_type');
        $deleted   = $request->get('deleted');
        $modified  = $request->get('modified');

        if (!$deleted && !$modified) {
            throw new InvalidArgumentException('No save data.');
        }

        if ($deleted) {
            $deleted = json_decode($deleted, true);
        }

        if ($modified) {
            $modified = json_decode($modified, true);
        }

        $contentRightsManager = $this->get('phlexible_access_control.rights');
        $permissions = $this->get('phlexible_access_control.permissions');

        $contentRights = $permissions->getAll();

        if ($deleted) {
            foreach ($deleted as $deletedRow) {
                $rights = array_keys($contentRights[$rightType][$deletedRow['content_type']]);

                foreach ($rights as $right) {
                    $contentRightsManager->removeRight(
                        $rightType,
                        $deletedRow['content_type'],
                        $deletedRow['content_id'],
                        $deletedRow['object_type'],
                        $deletedRow['object_id'],
                        $right,
                        $deletedRow['language']
                    );
                }
            }
        }

        if ($modified) {
            foreach ($modified as $modifiedRow) {
                foreach ($modifiedRow['rights'] as $rightRow) {
                    // if name of right is not present (e.g. component
                    // was deinstalled) do not save the right
                    if (empty($rightRow['right'])) {
                        continue;
                    }

                    $contentRightsManager->removeRight(
                        $rightType,
                        $modifiedRow['content_type'],
                        $modifiedRow['content_id'],
                        $modifiedRow['object_type'],
                        $modifiedRow['object_id'],
                        $rightRow['right'],
                        $modifiedRow['language']
                    );

                    if (!in_array($rightRow['status'], [
                        \Phlexible\Component\AccessControl\Rights\Rights::RIGHT_STATUS_INHERITABLE,
                        \Phlexible\Component\AccessControl\Rights\Rights::RIGHT_STATUS_SINGLE,
                        \Phlexible\Component\AccessControl\Rights\Rights::RIGHT_STATUS_STOPPED
                    ])) {
                        continue;
                    }

                    $contentRightsManager->setRight(
                        $rightType,
                        $modifiedRow['content_type'],
                        $modifiedRow['content_id'],
                        $modifiedRow['object_type'],
                        $modifiedRow['object_id'],
                        $rightRow['right'],
                        $rightRow['status'],
                        $modifiedRow['language']
                    );
                }
            }
        }

        return new ResultResponse(true, 'Rights saved.');
    }
}
