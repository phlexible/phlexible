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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * List controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elementtypes/list")
 * @Security("is_granted('elementtypes')")
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
        $elementtypeService = $this->get('phlexible_elementtype.service');

        $type = $request->get('type', Elementtype::TYPE_FULL);

        $elementtypes = $elementtypeService->findElementtypeByType($type);

        $list = array();
        foreach ($elementtypes as $elementtype) {
            //if ($type === null && !in_array($elementtype->getType(), $allowedTypes)) {
            //    continue;
            //}

            $elementtypeVersion = $elementtypeService->findLatestElementtypeVersion($elementtype);

            $list[$elementtype->getTitle() . $elementtype->getId()] = array(
                'id'      => $elementtype->getId(),
                'title'   => $elementtype->getTitle(),
                'icon'    => $elementtype->getIcon(),
                'version' => $elementtypeVersion->getVersion(),
                'type'    => $elementtype->getType(),
            );
        }

        ksort($list);
        $elementTypes = array_values($list);

        return new JsonResponse(array(
            'elementtypes' => $elementTypes,
            'total'        => count($elementTypes)
        ));
    }

    /**
     * List elementtypes for type
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/fortype", name="elementtypes_list_fortype")
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
     * @Route("/versions", name="elementtypes_list_versions")
     */
    public function versionsAction(Request $request)
    {
        $id = $request->get('id');

        $elementtypeService = $this->get('phlexible_elementtype.service');
        $userManager = $this->get('phlexible_user.user_manager');

        $elementtype = $elementtypeService->findElementtype($id);
        $versionList = $elementtypeService->getVersions($elementtype);

        $versions = array();
        foreach ($versionList as $version) {
            $versionElementtypeVersion = $elementtypeService->findElementtypeVersion($elementtype, $version);

            try {
                $user = $userManager->find($versionElementtypeVersion->getCreateUserId());
                $username = $user->getUsername();
            } catch (\Exception $e) {
                $username = '(unknown)';
            }

            $versions[] = array(
                'version'     => $version,
                'create_user' => $username,
                'create_time' => $versionElementtypeVersion->getCreatedAt()->format('Y-m-d H:i:s'),
            );
        }

        return new JsonResponse(array('versions' => $versions));
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
        $title    = $request->get('title');
        $type     = $request->get('type');
        $uniqueId = $request->get('unique_id');

        if (!$uniqueId) {
            $uniqueId = strtolower($title);
            $uniqueId = preg_replace('[^a-z0-9_]', '_', $uniqueId);
        }

        $elementtypeService = $this->get('phlexible_elementtype.service');
        $elementtypeService->create($type, $uniqueId, $title, null, $this->getUser()->getId());

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

        $elementtypeService = $this->get('phlexible_elementtype.service');
        $elementtypeService->delete($id);

        return new ResultResponse(true, 'Element Type deleted.');
    }

    /**
     * Duplicate elementtype
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/delete", name="elementtypes_list_delete")
     */
    public function duplicateAction(Request $request)
    {
        $id = $request->get('id');

        $db = $this->get('connection_manager')->default;
        $db->beginTransaction();

        $elementtypeService = $this->get('phlexible_elementtype.service');
        $sourceElementtype = $elementtypeService->findElementtype($id);
        $elementtype = $elementtypeService->duplicate($sourceElementtype);

        $db->commit();

        return new ResultResponse(true, 'Element duplicated.');
    }
}
