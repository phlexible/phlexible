<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Usage controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elementtypes/usage")
 * @Security("is_granted('elementtypes')")
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

        $usages = array();
        foreach ($usageManager->getUsage($elementtype) as $usage) {
            $usages[] = array(
                'type'           => $usage->getType(),
                'as'             => $usage->getAs(),
                'id'             => $usage->getId(),
                'title'          => $usage->getTitle(),
                'latest_version' => $usage->getLatestVersion(),
            );
        }

        return new JsonResponse(array('list' => $usages, 'total' => count($usages)));
    }
}