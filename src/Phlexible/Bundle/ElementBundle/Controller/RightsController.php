<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Phlexible\Component\AccessControl\Domain\AccessControlList;
use Phlexible\Component\AccessControl\Model\HierarchicalObjectIdentity;
use Phlexible\Component\AccessControl\Permission\HierarchyMaskResolver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Rights controller.
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
     *
     * @throws \Exception
     * @Route("/subjects", name="elements_rights_identities")
     */
    public function identitiesAction(Request $request)
    {
        $objectType = $request->get('objectType');
        $objectId = $request->get('objectId', null);

        if ($objectType === 'teaser') {
            $path = array($objectId);
        } elseif ($objectType === 'Phlexible\Bundle\TreeBundle\Entity\TreeNode') {
            $tree = $this->get('phlexible_tree.tree_manager')->getByNodeId($objectId);
            $node = $tree->get($objectId);
            $identity = HierarchicalObjectIdentity::fromDomainObject($node);
        } else {
            throw new \Exception("Unsupported object type $objectType");
        }

        $accessManager = $this->get('phlexible_access_control.access_manager');

        $acl = $accessManager->findAcl($identity);

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
}
