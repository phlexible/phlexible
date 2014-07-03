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
 * @Route("/mediamanager/rights")
 * @Security("is_granted('media')")
 */
class RightsController extends Controller
{
    /**
     * List subjects
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/subjects", name="mediamanager_rights_subjects")
     */
    public function subjectsAction(Request $request)
    {
        $rightType = $request->get('right_type', null);
        $contentType = $request->get('content_type', null);
        $contentId = $request->get('content_id', null);

        $site = $this->get('mediasite.manager')->getByFolderId($contentId);
        $folder = $site->findFolder($contentId);
        $path = array($folder->getId());
        $pathFolder = $folder;
        while ($pathFolder->getParentId()) {
            array_unshift($path, $pathFolder->getParentId());
            $pathFolder = $site->findFolder($pathFolder->getParentId());
        };

        $contentRightsManager = $this->get('phlexible_access_control.rights');
        $userManager = $this->get('phlexible_user.user_manager');
        $groupManager = $this->get('phlexible_user.group_manager');

        $subjects = $contentRightsManager->getRights(
            array('uid', 'gid'),
            $rightType,
            $contentType,
            $contentId,
            $path,
            array(
                'uid' => function (array $ids) use ($userManager) {
                    $users = $userManager->findBy(array('uid' => $ids));

                    $subjects = array();
                    foreach ($users as $user) {
                        $subjects['uid__' . $user->getId()] = $user->getFirstname() . ' ' . $user->getLastname();
                    }

                    return $subjects;
                },
                'gid' => function (array $ids) use ($groupManager) {
                    $groups = $groupManager->findBy(array('gid' => $ids));

                    $subjects = array();

                    foreach ($groups as $group) {
                        $subjects['gid__' . $group->getId()] = $group->getName();
                    }

                    return $subjects;
                }
            )
        );

        return new JsonResponse(array('subjects' => $subjects));
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

        $site = $this->get('mediasite.manager')->getByFolderId($contentId);
        $folder = $site->findFolder($contentId);
        $path = array($folder->getId());
        $pathFolder = $folder;
        while ($pathFolder->getParentId()) {
            array_unshift($path, $pathFolder->getParentId());
            $pathFolder = $site->findFolder($pathFolder->getParentId());
        };

        $abovePath = array();
        if (count($path)) {
            $abovePath = $path;
            array_pop($abovePath);
        }

        $contentRightsHelper = $this->get('accesscontrol.right.registry');
        $contentRights = array_keys($contentRightsHelper->getRights($rightType, $contentType));
        $rights = array();
        foreach ($contentRights as $right) {
            $rights[$right] = array(
                'right'  => $right,
                'status' => -1,
                'info'   => '',
            );
        }

        $subject = null;

        if ($objectType === 'uid') {
            $user = $this->get('users.repository')->find($objectId);

            $subject = array(
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
            );
        } elseif ($objectType === 'gid') {
            $group = $this->get('users.group.repository')->find($objectId);

            $subject = array(
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
            );
        }

        return new JsonResponse($subject);
    }
}
