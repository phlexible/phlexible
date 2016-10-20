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

use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * List controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elementtypes/list")
 * @Security("is_granted('ROLE_ELEMENTTYPES')")
 */
class ListController extends Controller
{
    /**
     * List elementtypes
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="elementtypes_list")
     */
    public function listAction(Request $request)
    {
        $type = $request->get('type', Elementtype::TYPE_FULL);

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');

        $elementtypes = [];
        foreach ($elementtypeService->findAllElementtypes() as $elementtype) {
            if ($type !== $elementtype->getType()) {
                continue;
            }

            if ($elementtype->getDeleted()) {
                continue;
            }

            $elementtypes[$elementtype->getTitle() . $elementtype->getId()] = [
                'id'      => $elementtype->getId(),
                'title'   => $elementtype->getTitle(),
                'icon'    => $elementtype->getIcon(),
                'version' => $elementtype->getRevision(),
                'type'    => $elementtype->getType(),
            ];
        }

        ksort($elementtypes);
        $elementtypes = array_values($elementtypes);

        $checker = $this->get('phlexible_element.checker');
        $changes = $checker->check();
        $hasChanges = false;
        foreach ($changes as $change) {
            if ($change->getNeedImport()) {
                $hasChanges = true;
                break;
            }
        }

        return new JsonResponse([
            'elementtypes' => $elementtypes,
            'total'        => count($elementtypes),
            'changes'      => $hasChanges,
        ]);
    }

    /**
     * Create an elementtype
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="elementtypes_list_create")
     */
    public function createAction(Request $request)
    {
        $title = $request->get('title');
        $type = $request->get('type');
        $uniqueId = $request->get('unique_id');

        if (!$uniqueId) {
            $uniqueId = preg_replace('/[^a-z0-9_]/', '_', strtolower($title));
        }

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $elementtypeService->createElementtype($type, $uniqueId, $title, null, null, [], $this->getUser()->getId());

        return new ResultResponse(true, 'Element Type created.');
    }

    /**
     * Delete an elementtype
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/delete", name="elementtypes_list_delete")
     */
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $elementtype = $elementtypeService->findElementtype($id);

        $elementtypeService->deleteElementtype($elementtype);

        return new ResultResponse(true, "Element type $id soft deleted.");
    }

    /**
     * Duplicate elementtype
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/duplicate", name="elementtypes_list_duplicate")
     */
    public function duplicateAction(Request $request)
    {
        $id = $request->get('id');

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $sourceElementtype = $elementtypeService->findElementtype($id);

        $elementtype = $elementtypeService->duplicateElementtype($sourceElementtype, $this->getUser()->getUsername());

        return new ResultResponse(true, 'Element type duplicated.');
    }
}
