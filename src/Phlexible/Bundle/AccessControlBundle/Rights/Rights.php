<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Rights;

use Phlexible\Bundle\AccessControlBundle\Model\AccessManagerInterface;
use Phlexible\Bundle\AccessControlBundle\Permission\PermissionCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Rights
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Marco Fischer <mf@brainbits.net>
 */
class Rights
{
    const RIGHT_STATUS_UNSET         = -1;
    const RIGHT_STATUS_STOPPED       =  0;
    const RIGHT_STATUS_SINGLE        =  1;
    const RIGHT_STATUS_INHERITABLE   =  2;
    const RIGHT_STATUS_INHERITED     =  3;
    const RIGHT_STATUS_STOPPED_UNSET =  4;

    /**
     * @var AccessManagerInterface
     */
    private $accessManager;

    /**
     * @var PermissionCollection
     */
    private $permissions;

    /**
     * @param AccessManagerInterface $accessManager
     * @param PermissionCollection   $permissions
     */
    public function __construct(AccessManagerInterface $accessManager, PermissionCollection $permissions)
    {
        $this->accessManager = $accessManager;
        $this->permissions = $permissions;
    }

    /**
     * @param array  $subjectTypes
     * @param string $rightType
     * @param string $contentType
     * @param string $contentId
     * @param array  $path
     * @param array  $subjectFetchers
     *
     * @return array
     * @throws \Exception
     */
    public function getRights(array $subjectTypes, $rightType, $contentType, $contentId, array $path, array $subjectFetchers)
    {
        $baseContentId = current($path);

        $rights = $this->accessManager->findBy(
            array(
                'object_type'  => $subjectTypes,
                'right_type'   => $rightType,
                'content_type' => $contentType,
                'content_id'   => $path
            )
        );

        $sort = array();
        foreach ($rights as $key => $right) {
            $sort[$key] = array_search((integer) $right['content_id'], $path);

            $rights[$key]['status'] = $rights[$key]['inherit'];
            unset($rights[$key]['inherit']);
            $rights[$key]['inherited'] = count($path) > 1 ? 1 : 0;
            if ($baseContentId != $right['content_id']) {
                $rights[$key]['inherited'] = 2;
            }
        }

        array_multisort($sort, $rights);

        $userIds  = array();
        $groupIds = array();

        foreach ($rights as $right) {
            if ($right['object_type'] == 'uid' && !array_key_exists($right['object_id'], $userIds)) {
                $userIds[$right['object_id']] = $right['object_id'];
            } elseif ($right['object_type'] == 'gid' && !array_key_exists($right['object_id'], $groupIds)) {
                $groupIds[$right['object_id']] = $right['object_id'];
            }
        }

        $userSubjects = array();
        if (count($userIds)) {
            $userSubjects = $subjectFetchers['uid']($userIds);
        }

        $groupSubjects = array();
        if (count($groupIds)) {
            $groupSubjects = $subjectFetchers['gid']($groupIds);
        }

        $subjectsData = array_merge($userSubjects, $groupSubjects);

        $contentRights = array_keys($this->permissions->getPermissions($rightType, $contentType));

        return $this->_getRightsForSubjects(
            $contentId,
            $subjectsData,
            $contentRights,
            $rights
        );
    }

    private function _getRightsForSubjects($contentId, array $subjectsData, array $allRights, array $rightsData)
    {
        $subjects = array();

        $allRights = array_flip($allRights);
        foreach ($allRights as $right => $rightsRow) {
            $allRights[$right] = array(
                'right'  => $right,
                'status' => self::RIGHT_STATUS_UNSET,
                'info'   => 'not_set',
            );
        }

        foreach ($rightsData as $rightsRow) {
            $objectType  = $rightsRow['object_type'];
            $objectId    = $rightsRow['object_id'];
            if (empty($subjectsData[$objectType . '__' . $objectId])) {
                continue;
            }
            $objectLabel = $subjectsData[$objectType . '__' . $objectId];
            $language    = $rightsRow['content_language'] ? $rightsRow['content_language'] : '_all_';
            $right       = $rightsRow['right'];
            $status      = $rightsRow['status'];
            $key         = $objectType.'__'.$objectId.'__'.$language;

            if (empty($subjects[$key])) {
                $subjects[$key] = array(
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
                );
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
            if ($status == self::RIGHT_STATUS_INHERITABLE) {
                if ($rightsRow['content_id'] != $contentId) {
                    $subjects[$key]['rights'][$right]['info']   = 'from_folder '.$rightsRow['content_id'];//$t9n->from_tid($rightsRow['content_id']);
                    $subjects[$key]['rights'][$right]['status'] = self::RIGHT_STATUS_INHERITED;
                    $subjects[$key]['above'][$right]['info']    = 'from_folder '.$rightsRow['content_id'];//$t9n->from_tid($rightsRow['content_id']);
                    $subjects[$key]['above'][$right]['status']  = self::RIGHT_STATUS_INHERITED;
                } else {
                    $subjects[$key]['rights'][$right]['info'] = 'defined_here';//$t9n->defined_here;
                }
            } elseif ($status == self::RIGHT_STATUS_SINGLE) {
                $subjects[$key]['rights'][$right]['info'] = 'stopped_below';//$t9n->stopped_below;
                if ($rightsRow['content_id'] != $contentId) {
                    $subjects[$key]['rights'][$right]['info']   = 'stopped_below';//$t9n->stopped_below;
                    $subjects[$key]['rights'][$right]['status'] = self::RIGHT_STATUS_STOPPED_UNSET;
                    $subjects[$key]['above'][$right]['info']    = 'stopped_below';//$t9n->stopped_below;
                    $subjects[$key]['above'][$right]['status']  = self::RIGHT_STATUS_STOPPED_UNSET;
                }
            } elseif ($status == self::RIGHT_STATUS_STOPPED) {
                $subjects[$key]['rights'][$right]['info'] = 'stopped_here';//$t9n->stopped_here;
                if ($rightsRow['content_id'] != $contentId) {
                    $subjects[$key]['rights'][$right]['info']   = 'stopped_here';//$t9n->stopped_here;
                    $subjects[$key]['rights'][$right]['status'] = self::RIGHT_STATUS_STOPPED_UNSET;
                    $subjects[$key]['above'][$right]['info']    = 'stopped_here';//$t9n->stopped_here;
                    $subjects[$key]['above'][$right]['status']  = self::RIGHT_STATUS_STOPPED_UNSET;
                }
            } elseif ($status == self::RIGHT_STATUS_STOPPED_UNSET) {
                $subjects[$key]['rights'][$right]['info'] = 'stopped_above';//$t9n->stopped_above;
                if ($rightsRow['content_id'] != $contentId) {
                    $subjects[$key]['above'][$right]['info'] = 'stopped_above';//$t9n->stopped_above;
                }
            }
        }

        foreach ($subjects as $key => $subjectRow) {
            $subjects[$key]['original'] = $subjects[$key]['rights'];
        }

        return array_values($subjects);
    }
}
