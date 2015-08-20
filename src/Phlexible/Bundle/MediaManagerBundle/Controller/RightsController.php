<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Component\AccessControl\Model\HierarchicalObjectIdentity;
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
     * @Route("/identities", name="mediamanager_rights_identities")
     */
    public function identitiesAction(Request $request)
    {
        $objectType = $request->get('objectType');
        $objectId = $request->get('objectId', null);

        $subjects = array();

        if ($objectType === 'teaser') {
            $path = array($objectId);
        } elseif ($objectType === 'Phlexible\Bundle\MediaManagerBundle\Entity\Folder') {
            $volume = $this->get('phlexible_media_manager.volume_manager')->getByFolderId($objectId);
            $folder = $volume->findFolder($objectId);
            $identity = HierarchicalObjectIdentity::fromDomainObject($folder);
        } else {
            throw new \Exception("Unsupported object type $objectType");
        }

        $accessManager = $this->get('phlexible_access_control.access_manager');

        /*
        $permissionRegistry = $this->get('phlexible_access_control.permission_registry');
        $permissions = array();
        foreach ($permissionRegistry->get($objectType) as $permissionCollection) {
            foreach ($permissionCollection->all() as $permission) {
                $permissions[] = $permission->getName();
            }
        }
        */

        $acl = $accessManager->findAcl($identity);

        $identities = array();

        if ($acl) {
            $resolver = $this->get('phlexible_access_control.security_resolver');

            foreach ($acl->getEntries() as $ace) {
                $identities[] = array(
                    'id'             => $ace->getId(),
                    'objectType'     => $acl->getObjectIdentity()->getType(),
                    'objectId'       => $acl->getObjectIdentity()->getIdentifier(),
                    'mask'           => $ace->getMask(),
                    'stopMask'       => $ace->getStopMask(),
                    'noInheritMask'  => $ace->getNoInheritMask(),
                    'objectLanguage' => null,
                    'securityType'   => $ace->getSecurityType(),
                    'securityId'     => $ace->getSecurityIdentifier(),
                    'securityName'   => $resolver->resolveName($ace->getSecurityType(), $ace->getSecurityIdentifier()),
                );
            }
        }

        return new JsonResponse(array('identities' => $identities));

        $rightType = $request->get('right_type', null);
        $contentType = $request->get('content_type', null);
        $contentId = $request->get('content_id', null);

        $volume = $this->get('phlexible_media_manager.volume_manager')->getByFolderId($contentId);
        $folder = $volume->findFolder($contentId);
        $path = [$folder->getId()];
        $pathFolder = $folder;
        while ($pathFolder->getParentId()) {
            array_unshift($path, $pathFolder->getParentId());
            $pathFolder = $volume->findFolder($pathFolder->getParentId());
        };

        $contentRightsManager = $this->get('phlexible_access_control.rights');
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
