<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Rights controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/folder/{$folderId}")
 * @Security("is_granted('ROLE_MEDIA')")
 */
class RightsController extends Controller
{
    /**
     * List subjects
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/permissions", name="mediamanager_folder_permissions")
     */
    public function permissionsAction(Request $request, $folderId)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $volume = $volumeManager->getByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        $accessManager = $this->get('phlexible_access_control.access_manager');
        $acl = $accessManager->getAcl($folder);

        return new JsonResponse($acl->getPermissions());

        $userManager = $this->get('phlexible_user.user_manager');
        $groupManager = $this->get('phlexible_user.group_manager');

        $subjects = $contentRightsManager->getRights(
            ['uid', 'gid'],
            $rightType,
            $contentType,
            $contentId,
            $path,
            [
                'uid' => function (array $ids) use ($userManager) {
                    $users = $userManager->findBy(['uid' => $ids]);

                    $subjects = [];
                    foreach ($users as $user) {
                        $subjects['uid__' . $user->getId()] = $user->getFirstname() . ' ' . $user->getLastname();
                    }

                    return $subjects;
                },
                'gid' => function (array $ids) use ($groupManager) {
                    $groups = $groupManager->findBy(['gid' => $ids]);

                    $subjects = [];

                    foreach ($groups as $group) {
                        $subjects['gid__' . $group->getId()] = $group->getName();
                    }

                    return $subjects;
                }
            ]
        );

        return new JsonResponse(['subjects' => $subjects]);
    }

    /**
     * Add subject
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/add", name="mediamanager_rights_add")
     */
    public function addAction(Request $request)
    {
        $rightType = $request->get('right_type', null);
        $contentType = $request->get('content_type', null);
        $contentId = $request->get('content_id', null);
        $objectType = $request->get('object_type', null);
        $objectId = $request->get('object_id', null);

        $volume = $this->get('phlexible_media_manager.volume_manager')->getByFolderId($contentId);
        $folder = $volume->findFolder($contentId);
        $path = [$folder->getId()];
        $pathFolder = $folder;
        while ($pathFolder->getParentId()) {
            array_unshift($path, $pathFolder->getParentId());
            $pathFolder = $volume->findFolder($pathFolder->getParentId());
        };

        $abovePath = [];
        if (count($path)) {
            $abovePath = $path;
            array_pop($abovePath);
        }

        $permissions = $this->get('phlexible_access_control.permissions');
        $contentRights = array_keys($permissions->getByType("$contentType-$rightType"));
        $rights = [];
        foreach ($contentRights as $right) {
            $rights[$right] = [
                'right'  => $right,
                'status' => -1,
                'info'   => '',
            ];
        }

        $subject = null;

        if ($objectType === 'uid') {
            $user = $this->get('phlexible_user.user_manager')->find($objectId);

            $subject = [
                'type'        => 'user',
                'object_type' => 'uid',
                'object_id'   => $objectId,
                'label'       => $user->getFirstname() . ' ' . $user->getLastname(),
                'rights'      => $rights,
                'original'    => $rights,
                'above'       => $rights,
                'language'    => '_all_',
                'inherited'   => 0,
                'restore'     => 0,
            ];
        } elseif ($objectType === 'gid') {
            $group = $this->get('phlexible_user.group_manager')->find($objectId);

            $subject = [
                'type'        => 'group',
                'object_type' => 'gid',
                'object_id'   => $objectId,
                'label'       => $group->getName(),
                'rights'      => $rights,
                'original'    => $rights,
                'above'       => $rights,
                'language'    => '_all_',
                'inherited'   => 0,
                'restore'     => 0,
            ];
        }

        return new JsonResponse($subject);
    }
}
