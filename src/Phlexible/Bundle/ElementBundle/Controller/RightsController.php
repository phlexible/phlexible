<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Rights controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/rights")
 * @Security("is_granted('ROLE_ELEMENTS')")
 */
class RightsController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/add", name="elements_rights_add")
     */
    public function addAction(Request $request)
    {
        $contentClass = $request->get('contentClass', null);
        $contentId = $request->get('contentId', null);
        $objectType = $request->get('objectType', null);
        $objectId = $request->get('objectId', null);

        $abovePath = [];
        if ($contentClass === 'teaser') {
            $path = [$contentId];
        } else {
            $tree = $this->get('phlexible_tree.tree_manager')->getByNodeId($contentId);
            $node = $tree->get($contentId);
            $path = $tree->getIdPath($node);

            if (count($path)) {
                $abovePath = $path;
                array_pop($abovePath);
            }
        }

        $permissions = $this->get('phlexible_access_control.permissions');
        $rights = [];
        foreach ($permissions->getByObjectType($contentClass) as $permission) {
            $rights[$permission->getName()] = [
                'right'  => $permission->getName(),
                'status' => -1,
                'info'   => '',
            ];
        }

        $subject = null;

        if ($objectType === 'uid') {
            $userProvider = $this->get('phlexible_access_control.provider.user');
            $name = $userProvider->getName($objectType, $objectId);

            $subject = [
                'type'       => 'user',
                'objectType' => 'uid',
                'objectId'   => $objectId,
                'label'      => $name,
                'rights'     => $rights,
                'original'   => $rights,
                'above'      => $rights,
                'language'   => '_all_',
                'inherited'  => 0,
                'restore'    => 0,
            ];
        } elseif ($objectType === 'gid') {
            $groupProvider = $this->get('phlexible_access_control.provider.group');
            $name = $groupProvider->getName($objectType, $objectId);

            $subject = [
                'type'       => 'group',
                'objectType' => 'gid',
                'objectId'   => $objectId,
                'label'      => $name,
                'rights'     => $rights,
                'original'   => $rights,
                'above'      => $rights,
                'language'   => '_all_',
                'inherited'  => 0,
                'restore'    => 0,
            ];
        }

        return new JsonResponse($subject);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/subjects", name="elements_rights_subjects")
     */
    public function subjectsAction(Request $request)
    {
        $contentClass = $request->get('contentClass');
        $contentId = $request->get('contentId', null);

        $subjects = [];

        if ($contentClass === 'teaser') {
            $path = [$contentId];
        } else {
            $tree = $this->get('phlexible_tree.tree_manager')->getByNodeId($contentId);
            $node = $tree->get($contentId);
            $path = $tree->getIdPath($node);
        }

        $userManager = $this->get('phlexible_user.user_manager');
        $accessManager = $this->get('phlexible_access_control.access_manager');
        $permissions = $this->get('phlexible_access_control.permissions');
        $contentRights = [];
        foreach ($permissions->getByObjectType($contentClass) as $permission) {
            $contentRights[] = $permission->getName();
        }

        $rightsData = $accessManager->findBy(array('contentClass' => $contentClass, 'contentId' => $contentId));
        return new JsonResponse(['subjects' => $rightsData]);
        dump($rightsData);
        die;
        $rightsData = $contentRightsManager->getRightsData(['uid', 'gid'], $rightType, $contentType, $path);

        $contentRights = array_keys($contentRightsHelper->getRights($rightType, $contentType));

        $rightsData = [1];
        if (count($rightsData)) {
            $userIds = [];
            $groupIds = [];

            foreach ($rightsData as $rightsRow) {
                if ($rightsRow['object_type'] == 'uid' && !array_key_exists($rightsRow['object_id'], $userIds)) {
                    $userIds[] = $rightsRow['object_id'];
                } elseif ($rightsRow['object_type'] == 'gid' && !array_key_exists($rightsRow['object_id'], $groupIds)) {
                    $groupIds[] = $rightsRow['object_id'];
                }
            }

            $subjectsData = [];
            if (count($userIds)) {
                $users = $userManager->findBy(['id' => $userIds]);
                foreach ($users as $user) {
                    $subjectsData["uid__{$user->getId()}"] = $user->getDisplayName();
                }
            }

            if (count($groupIds)) {
                $groups = $userManager->findBy(['id' => $userIds]);
                foreach ($groups as $group) {
                    $subjectsData["gid__{$group->getId()}"] = $group->getName();
                }
            }

            $subjects = array_merge(
                $subjects,
                $this->getRightsForSubjects(
                    $contentType,
                    $contentId,
                    $subjectsData,
                    $path,
                    $contentRights,
                    $rightsData
                )
            );
        }

        return new JsonResponse(['subjects' => $subjects]);
    }

    /**
     * @param string $contentType
     * @param string $contentId
     * @param array  $subjectsData
     * @param array  $path
     * @param array  $allRights
     * @param array  $rightsData
     *
     * @return array
     */
    protected function getRightsForSubjects(
        $contentType,
        $contentId,
        array $subjectsData,
        array $path,
        array $allRights,
        array $rightsData)
    {
        $subjects = [];

        $t9n = $this->getContainer()->t9n->elements;

        $allRights = array_flip($allRights);
        foreach ($allRights as $right => $rightsRow) {
            $allRights[$right] = [
                'right'  => $right,
                'status' => Phlexible\Bundle\AccessControlBundle\Rights::RIGHT_STATUS_UNSET,
                'info'   => $t9n->not_set,
            ];
        }

        foreach ($rightsData as $rightsRow) {
            $objectType = $rightsRow['object_type'];
            $objectId = $rightsRow['object_id'];
            if (empty($subjectsData[$objectType . '__' . $objectId])) {
                continue;
            }
            $objectLabel = $subjectsData[$objectType . '__' . $objectId];
            $language = $rightsRow['content_language'] ? $rightsRow['content_language'] : '_all_';
            $right = $rightsRow['right'];
            $status = $rightsRow['status'];
            $key = $objectType . '__' . $objectId . '__' . $language;

            if (empty($subjects[$key])) {
                $subjects[$key] = [
                    'type'        => $objectType === 'uid' ? 'user' : 'group',
                    'object_type' => $objectType,
                    'object_id'   => $objectId,
                    'label'       => $objectLabel,
                    'language'    => $language,
                    'rights'      => $allRights,
                    'original'    => null,
                    'above'       => $allRights,
                    'inherited'   => 0,
                    'set_here'    => 1,
                    'restore'     => 0,
                ];
            }

            $subjects[$key]['rights'][$right]['status'] = $status;
            if ($rightsRow['content_id'] != $contentId) {
                $subjects[$key]['rights'][$right]['above'] = $status;
            }

            if ($contentId !== $rightsRow['content_id']) {
                $subjects[$key]['set_here'] = 0;
                $subjects[$key]['inherited'] = 1;
            }

            /*
            if ($rightsRow['inherited'])
            {
                $subjects[$key]['set_here'] = 0;

                if ($rightsRow['inherited'] > 1)
                {
                    $subjects[$key]['inherited'] = 1;
                }
            }
            */

            if ($status == Phlexible\Bundle\AccessControlBundle\Rights::RIGHT_STATUS_INHERITABLE) {
                if ($rightsRow['content_id'] != $contentId) {
                    $subjects[$key]['rights'][$right]['info'] = $t9n->from_tid($rightsRow['content_id']);
                    $subjects[$key]['rights'][$right]['status'] = Phlexible\Bundle\AccessControlBundle\Rights::RIGHT_STATUS_INHERITED;
                    $subjects[$key]['above'][$right]['info'] = $t9n->from_tid($rightsRow['content_id']);
                    $subjects[$key]['above'][$right]['status'] = Phlexible\Bundle\AccessControlBundle\Rights::RIGHT_STATUS_INHERITED;
                } else {
                    $subjects[$key]['rights'][$right]['info'] = $t9n->defined_here;
                }
            } elseif ($status == Phlexible\Bundle\AccessControlBundle\Rights::RIGHT_STATUS_SINGLE) {
                $subjects[$key]['rights'][$right]['info'] = $t9n->stopped_below;
                if ($rightsRow['content_id'] != $contentId) {
                    $subjects[$key]['rights'][$right]['info'] = $t9n->stopped_below;
                    $subjects[$key]['rights'][$right]['status'] = Phlexible\Bundle\AccessControlBundle\Rights::RIGHT_STATUS_STOPPED_UNSET;
                    $subjects[$key]['above'][$right]['info'] = $t9n->stopped_below;
                    $subjects[$key]['above'][$right]['status'] = Phlexible\Bundle\AccessControlBundle\Rights::RIGHT_STATUS_STOPPED_UNSET;
                }
            } elseif ($status == Phlexible\Bundle\AccessControlBundle\Rights::RIGHT_STATUS_STOPPED) {
                $subjects[$key]['rights'][$right]['info'] = $t9n->stopped_here;
                if ($rightsRow['content_id'] != $contentId) {
                    $subjects[$key]['rights'][$right]['info'] = $t9n->stopped_here;
                    $subjects[$key]['rights'][$right]['status'] = Phlexible\Bundle\AccessControlBundle\Rights::RIGHT_STATUS_STOPPED_UNSET;
                    $subjects[$key]['above'][$right]['info'] = $t9n->stopped_here;
                    $subjects[$key]['above'][$right]['status'] = Phlexible\Bundle\AccessControlBundle\Rights::RIGHT_STATUS_STOPPED_UNSET;
                }
            } elseif ($status == Phlexible\Bundle\AccessControlBundle\Rights::RIGHT_STATUS_STOPPED_UNSET) {
                $subjects[$key]['rights'][$right]['info'] = $t9n->stopped_above;
                if ($rightsRow['content_id'] != $contentId) {
                    $subjects[$key]['above'][$right]['info'] = $t9n->stopped_above;
                }
            }
        }

        foreach ($subjects as $key => $subjectRow) {
            $subjects[$key]['original'] = $subjects[$key]['rights'];
        }


        return array_values($subjects);
    }
}
