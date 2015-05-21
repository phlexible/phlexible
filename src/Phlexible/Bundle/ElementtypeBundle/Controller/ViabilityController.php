<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Component\Elementtype\Model\Elementtype;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Viability controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elementtypes/viability")
 * @Security("is_granted('ROLE_ELEMENTTYPES')")
 */
class ViabilityController extends Controller
{
    /**
     * List Element Types
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/fortype", name="elementtypes_viability_for_type")
     */
    public function fortypeAction(Request $request)
    {
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');

        $for = $request->get('type', Elementtype::TYPE_FULL);

        $elementtypes = $elementtypeService->findAllElementtypes();

        $allowedForFull = $allowedForStructure = $allowedForArea = [
            Elementtype::TYPE_FULL,
            Elementtype::TYPE_STRUCTURE
        ];
        $allowedForContainer = $allowedForPart = [
            Elementtype::TYPE_LAYOUTAREA,
            Elementtype::TYPE_LAYOUTCONTAINER
        ];

        $list = [];
        foreach ($elementtypes as $elementtype) {
            $type = $elementtype->getType();

            if ($for == Elementtype::TYPE_FULL && !in_array($type, $allowedForFull)) {
                continue;
            } elseif ($for == Elementtype::TYPE_STRUCTURE && !in_array($type, $allowedForStructure)) {
                continue;
            } elseif ($for == Elementtype::TYPE_REFERENCE) {
                continue;
            } elseif ($for == Elementtype::TYPE_LAYOUTAREA && !in_array($type, $allowedForArea)) {
                continue;
            } elseif ($for == Elementtype::TYPE_LAYOUTCONTAINER && !in_array($type, $allowedForContainer)) {
                continue;
            } elseif ($for == Elementtype::TYPE_PART && !in_array($type, $allowedForPart)) {
                continue;
            }

            $list[] = [
                'id'      => $elementtype->getId(),
                'type'    => $elementtype->getType(),
                'title'   => $elementtype->getTitle(),
                'icon'    => $elementtype->getIcon(),
                'version' => $elementtype->getRevision()
            ];

        }

        return new JsonResponse(['elementtypes' => $list, 'total' => count($list)]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/list", name="elementtypes_viability_list")
     */
    public function listAction(Request $request)
    {
        $id = $request->get('id');

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $viabilityManager = $this->get('phlexible_elementtype.viability_manager');
        $elementtype = $elementtypeService->findElementtype($id);

        $viabilities = [];
        foreach ($viabilityManager->findAllowedParents($elementtype) as $viability) {
            $viabilityElementtype = $elementtypeService->findElementtype($viability->getUnderElementtypeId());
            $viabilities[] = [
                'id'    => $viabilityElementtype->getId(),
                'title' => $viabilityElementtype->getTitle(),
                'icon'  => $viabilityElementtype->getIcon()
            ];
        }

        return new JsonResponse(['viability' => $viabilities]);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="elementtypes_viability_save")
     */
    public function saveAction(Request $request)
    {
        $id = $request->get('id');
        $ids = $request->get('ids');

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $elementtype = $elementtypeService->findElementtype($id);

        $elementtypeService->updateViability($elementtype, $ids);

        return new ResultResponse(true);
    }
}
