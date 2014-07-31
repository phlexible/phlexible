<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\ElementtypeBundle\Elementtype\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\ElementtypeVersion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Viability controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elementtypes/viability")
 * @Security("is_granted('elementtypes')")
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
        $elementtypeService = $this->get('phlexible_elementtype.service');

        $for = $request->get('type', Elementtype::TYPE_FULL);

        $elementtypes = $elementtypeService->findAllElementtypes();

        $allowedForFull = $allowedForStructure = $allowedForArea = array(
            Elementtype::TYPE_FULL,
            Elementtype::TYPE_STRUCTURE
        );
        $allowedForContainer = $allowedForPart = array(
            Elementtype::TYPE_LAYOUTAREA,
            Elementtype::TYPE_LAYOUTCONTAINER
        );

        $list = array();
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

            $elementTypeVersion = $elementtypeService->findLatestElementtypeVersion($elementtype);

            $list[] = array(
                'id'      => $elementtype->getId(),
                'type'    => $elementtype->getType(),
                'title'   => $elementtype->getTitle(),
                'icon'    => $elementtype->getIcon(),
                'version' => $elementTypeVersion->getVersion()
            );

        }

        return new JsonResponse(array('elementtypes' => $list, 'total' => count($list)));
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

        $elementtypeService = $this->get('phlexible_elementtype.service');
        $elementtype = $elementtypeService->findElementtype($id);

        $viability = array();
        foreach ($elementtypeService->findAllowedParentIds($elementtype) as $viabilityId) {
            $viabilityElementtype = $elementtypeService->findElementtype($viabilityId);
            $viability[] = array(
                'id'    => $viabilityId,
                'title' => $viabilityElementtype->getTitle(),
                'icon'  => $viabilityElementtype->getIcon()
            );
        }

        return new JsonResponse(array('viability' => $viability));
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

        $elementtypeService = $this->get('phlexible_elementtype.service');
        $elementtype = $elementtypeService->findElementtype($id);

        $elementtypeService->saveAllowedParentIds($elementtype, $ids);

        return new ResultResponse(true);
    }
}
