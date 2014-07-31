<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Rights controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/accesscontrol")
 * @Security("is_granted('accesscontrol')")
 */
class RightsController extends Controller
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
     * @Route("/rights", name="accesscontrol_rights")
     */
    public function rightsAction(Request $request)
    {
        $rightType   = $request->get('right_type');
        $contentType = $request->get('content_type');

        $permissions = $this->get('phlexible_access_control.permissions');
        $contentRights = array();
        foreach ($permissions->getByType("$contentType-$rightType") as $permission) {
            $contentRights[] = array(
                'name'    => $permission->getName(),
                'iconCls' => $permission->getIconClass(),
            );
        }

        return new JsonResponse($contentRights);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @throws \Exception
     * @Route("/save", name="accesscontrol_save")
     */
    public function saveAction(Request $request)
    {
        $rightType = $request->get('right_type');
        $deleted   = $request->get('deleted');
        $modified  = $request->get('modified');

        if (!$deleted && !$modified) {
            throw new \Exception('No save data.');
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

                    if (!in_array($rightRow['status'], array(
                        \Phlexible\Bundle\AccessControlBundle\Rights\Rights::RIGHT_STATUS_INHERITABLE,
                        \Phlexible\Bundle\AccessControlBundle\Rights\Rights::RIGHT_STATUS_SINGLE,
                        \Phlexible\Bundle\AccessControlBundle\Rights\Rights::RIGHT_STATUS_STOPPED
                    ))) {
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
