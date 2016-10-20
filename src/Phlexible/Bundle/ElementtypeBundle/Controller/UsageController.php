<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Usage controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elementtypes/usage")
 * @Security("is_granted('ROLE_ELEMENTTYPES')")
 */
class UsageController extends Controller
{
    /**
     * Show Usage of an Element Type
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/elementtypes/usage", name="elementtypes_usage")
     */
    public function listAction(Request $request)
    {
        $id = $request->get('id');

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $usageManager = $this->get('phlexible_elementtype.usage_manager');

        $elementtype = $elementtypeService->findElementtype($id);

        $usages = [];
        foreach ($usageManager->getUsage($elementtype) as $usage) {
            $usages[] = [
                'type'           => $usage->getType(),
                'as'             => $usage->getAs(),
                'id'             => $usage->getId(),
                'title'          => $usage->getTitle(),
                'latest_version' => $usage->getLatestVersion(),
            ];
        }

        return new JsonResponse(['list' => $usages, 'total' => count($usages)]);
    }
}
