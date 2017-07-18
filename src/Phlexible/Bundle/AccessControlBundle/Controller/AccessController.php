<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\AccessControlBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Component\AccessControl\Domain\AccessControlList;
use Phlexible\Component\AccessControl\Domain\Entry;
use Phlexible\Component\AccessControl\Exception\InvalidArgumentException;
use Phlexible\Component\AccessControl\Domain\HierarchicalObjectIdentity;
use Phlexible\Component\AccessControl\Permission\HierarchyMaskResolver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Access controller.
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
        $query = $request->get('query');
        $limit = $request->get('limit', 20);
        $offset = $request->get('start', 0);

        $userProvider = $this->get('phlexible_access_control.user_security_provider');

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
        $query = $request->get('query');
        $limit = $request->get('limit', 20);
        $offset = $request->get('start', 0);

        $userProvider = $this->get('phlexible_access_control.group_security_provider');

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
            $permissions[] = array(
                'name' => $permission->getName(),
                'bit' => $permission->getBit(),
                'iconCls' => 'null',
            );
        }

        return new JsonResponse(array('permissions' => $permissions));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     * @Route("/identities", name="accesscontrol_identities")
     */
    public function identitiesAction(Request $request)
    {
        $objectType = $request->get('objectType');
        $objectId = $request->get('objectId', null);

        $objectIdentityResolver = $this->get('phlexible_access_control.object_identity_resolver');
        $objectIdentity = $objectIdentityResolver->resolve($objectType, $objectId);

        if (!$objectIdentity) {
            throw new \InvalidArgumentException("Unsupported object type $objectType");
        }

        $accessManager = $this->get('phlexible_access_control.access_manager');

        $acl = $accessManager->findAcl($objectIdentity);

        return new JsonResponse(array('identities' => $this->createIdentities($acl)));
    }

    /**
     * @param AccessControlList $acl
     *
     * @return array
     */
    private function createIdentities(AccessControlList $acl)
    {
        $securityResolver = $this->get('phlexible_access_control.security_resolver');

        $identities = array();

        $oi = $acl->getObjectIdentity();
        if ($oi instanceof HierarchicalObjectIdentity) {
            $map = array();
            foreach ($oi->getHierarchicalIdentifiers() as $identifier) {
                foreach ($acl->getEntries() as $entry) {
                    if ($entry->getObjectIdentifier() === $identifier) {
                        $map[$entry->getSecurityType()][$entry->getSecurityIdentifier()][$entry->getObjectIdentifier()] = $entry;
                    }
                }
            }

            $maskResolver = new HierarchyMaskResolver();

            foreach ($map as $securityType => $securityIdentifiers) {
                foreach ($securityIdentifiers as $securityIdentifier => $entries) {
                    $resolvedMasks = $maskResolver->resolve($entries, $oi->getIdentifier());
                    $identities[] = array(
                        'id' => 0, //$ace->getId(),
                        'objectType' => $oi->getType(),
                        'objectId' => $oi->getIdentifier(),
                        'effectiveMask' => $resolvedMasks['effectiveMask'],
                        'mask' => $resolvedMasks['mask'],
                        'stopMask' => $resolvedMasks['stopMask'],
                        'noInheritMask' => $resolvedMasks['noInheritMask'],
                        'parentMask' => $resolvedMasks['parentMask'],
                        'parentStopMask' => $resolvedMasks['parentStopMask'],
                        'parentNoInheritMask' => $resolvedMasks['parentNoInheritMask'],
                        'objectLanguage' => null,
                        'securityType' => $securityType,
                        'securityId' => $securityIdentifier,
                        'securityName' => $securityResolver->resolveName($securityType, $securityIdentifier),
                    );
                }
            }
        } else {
            // TODO: implement
        }

        return $identities;
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     *
     * @throws \Exception
     * @Route("/save", name="accesscontrol_save")
     */
    public function saveAction(Request $request)
    {
        $objectType = $request->get('objectType');
        $objectId = $request->get('objectId');
        $data = $request->get('identities');

        if (!$data) {
            throw new InvalidArgumentException('No save data.');
        }

        $identities = json_decode($data, true);

        $objectIdentityResolver = $this->get('phlexible_access_control.object_identity_resolver');
        $objectIdentity = $objectIdentityResolver->resolve($objectType, $objectId);

        $accessManager = $this->get('phlexible_access_control.access_manager');
        $acl = $accessManager->findAcl($objectIdentity);

        if (!$acl) {
            $acl = $accessManager->createAcl($objectIdentity);
        }

        foreach ($acl->getEntries() as $ace) {
            $acl->removeEntry($ace);
        }
        foreach ($identities as $objectIdentity) {
            if ($objectIdentity['mask'] === null && $objectIdentity['stopMask'] === null && $objectIdentity['noInheritMask'] === null) {
                continue;
            }

            $ace = new Entry(
                $acl,
                $objectIdentity['objectType'],
                $objectIdentity['objectId'],
                $objectIdentity['securityType'],
                $objectIdentity['securityId'],
                $objectIdentity['mask'],
                $objectIdentity['stopMask'],
                $objectIdentity['noInheritMask']
            );

            $acl->addEntry($ace);
        }

        $accessManager->updateAcl($acl);

        return new ResultResponse(true, 'Rights saved.');
    }
}
