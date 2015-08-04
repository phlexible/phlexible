<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Controller;

use Phlexible\Bundle\ElementBundle\Model\ElementStructureIterator;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tree controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Marcus Stöhr <mstoehr@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 * @Route("/tree")
 */
class TreeController extends Controller
{
    const MODE_NOET_NOTARGET = 1;
    const MODE_NOET_TARGET = 2;
    const MODE_ET_NOTARGET = 3;
    const MODE_ET_TARGET = 4;

    /**
     * Return the Element data tree
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/tree", name="tree_tree")
     */
    public function treeAction(Request $request)
    {
        $siterootId = $request->get('siteroot_id');
        $tid = $request->get('node');
        $language = $request->get('language');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $nodeSerializer = $this->get('phlexible_tree.node_serializer');

        // TODO: switch to master language of element
        $defaultLanguage = $this->container->getParameter('phlexible_cms.languages.default');

        $tree = $treeManager->getBySiteRootId($siterootId);
        $rootNode = $tree->getRoot();

        $data = [];
        if ($rootNode) {
            if ($tid === null || $tid < 1) {
                $data = [$nodeSerializer->serializeNode($rootNode, $language)];
            } else {
                $node = $tree->get($tid);

                // check if children of this node should be shown
                $element = $elementService->findElement($node->getTypeId());
                $elementtype = $elementService->findElementtype($element);

                $nodes = $tree->getChildren($node);
                if (!empty($nodes) && !$elementtype->getHideChildren()) {
                    $data = $nodeSerializer->serializeNodes($nodes, $language);
                }
            }
        }

        return new JsonResponse($data);
    }

    /**
     * List all element types
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/childelementtypes", name="tree_childelementtypes")
     */
    public function childelementtypesAction(Request $request)
    {
        $eid = $request->get('eid');

        $elementService = $this->get('phlexible_element.element_service');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $element = $elementService->findElement($eid);
        $elementtype = $elementService->findElementtype($element);
        $childElementtypes = $elementService->findAllowedChildren($elementtype);

        $data = [];
        foreach ($childElementtypes as $childElementtype) {
            if (!in_array($childElementtype->getType(), [Elementtype::TYPE_FULL, Elementtype::TYPE_STRUCTURE])) {
                continue;
            }

            $data[$childElementtype->getTitle().'_'.$childElementtype->getId()] = [
                'id'    => $childElementtype->getId(),
                'title' => $childElementtype->getTitle(),
                'icon'  => $iconResolver->resolveElementtype($childElementtype),
                'type'  => $childElementtype->getType(),
            ];
        }
        ksort($data);
        $data = array_values($data);

        return new JsonResponse(['elementtypes' => $data]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/childelements", name="tree_childelements")
     */
    public function childelementsAction(Request $request)
    {
        $id = $request->get('id');
        $language = $request->get('language');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $tree = $treeManager->getByNodeID($id);
        $node = $tree->get($id);
        $eid = $node->getTypeId();
        $element = $elementService->findElement($eid);

        if (!$language) {
            $language = $element->getMasterLanguage();
        }

        $firstString = $this->get('translator')->trans('elements.first', [], 'gui');

        $data = [];
        $data[] = [
            'id'    => '0',
            'title' => $firstString,
            'icon'  => $iconResolver->resolveIcon('_top.gif'),
        ];

        foreach ($tree->getChildren($node) as $childNode) {
            $childElement = $elementService->findElement($childNode->getTypeId());
            $childElementVersion = $elementService->findLatestElementVersion($childElement);

            $data[] = [
                'id'    => $childNode->getId(),
                'title' => $childElementVersion->getBackendTitle($language, $childElementVersion->getElement()->getMasterLanguage()) . ' [' . $childNode->getId() . ']',
                'icon'  => $iconResolver->resolveTreeNode($childNode, $language),
            ];
        }

        return new JsonResponse(['elements' => $data]);
    }

    /**
     * Create an Element
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="tree_create")
     */
    public function createAction(Request $request)
    {
        $parentId = $request->get('id');
        $siterootId = $request->get('siteroot_id');
        $elementtypeId = $request->get('element_type_id');
        $afterId = $request->get('prev_id');
        $sortMode = $request->get('sort', 'title');
        $sortDir = $request->get('sort_dir', 'asc');
        $navigation = $request->get('navigation') ? true : false;
        $restricted = $request->get('restricted') ? true : false;
        $masterLanguage = $request->get('masterlanguage');

        $elementService = $this->get('phlexible_element.element_service');
        $treeManager = $this->get('phlexible_tree.tree_manager');

        $tree = $treeManager->getBySiteRootId($siterootId);
        $parentNode = $tree->get($parentId);
        $afterNode = $tree->get($afterId);

        $userId = $this->getUser()->getId();

        $elementSource = $elementService->findElementSource($elementtypeId);

        $element = $elementService->createElement($elementSource, $masterLanguage, $userId);

        $node = $tree->create(
            $parentNode,
            $afterNode,
            'element-' . $elementSource->getType(),
            $element->getEid(),
            [],
            $this->getUser()->getId(),
            $sortMode,
            $sortDir,
            $navigation,
            $restricted
        );

        return new ResultResponse(
            true,
            'Element EID "' . $element->getEid() . ' (' . $masterLanguage . ')" created.',
            [
                'eid'             => $element->getEid(),
                'tid'             => $node->getId(),
                'master_language' => $masterLanguage,
                'navigation'      => $navigation,
                'restricted'      => $restricted
            ]
        );
    }

    /**
     * Create an Element
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/createinstance", name="tree_create_instance")
     */
    public function createInstanceAction(Request $request)
    {
        $parentId = $request->get('id');
        $afterId = $request->get('prev_id');
        $sourceId = $request->get('for_tree_id');

        $treeManager = $this->get('phlexible_tree.tree_manager');

        $tree = $treeManager->getByNodeId($parentId);
        $parentNode = $tree->get($parentId);
        $prevNode = $tree->get($afterId);
        $sourceNode = $tree->get($sourceId);

        $tree->createInstance($parentNode, $prevNode, $sourceNode, $this->getUser()->getId());

        return new ResultResponse(true, 'Instance created.');
    }

    /**
     * Copy an Element
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/copy", name="tree_copy")
     */
    public function copyAction(Request $request)
    {
        $parentId = $request->get('id');
        $sourceId = $request->get('for_tree_id');
        $prevId = $request->get('prev_id');

        $elementService = $this->get('phlexible_element.element_service');
        $treeManager = $this->get('phlexible_tree.tree_manager');

        $sourceTree = $treeManager->getByNodeId($sourceId);
        $sourceNode = $sourceTree->get($sourceId);

        $targetTree = $treeManager->getByNodeId($parentId);
        $parentNode = $targetTree->get($parentId);
        $prevNode = $targetTree->get($prevId);

        $sourceElement = $elementService->findElement($sourceNode->getTypeId());
        $sourceElementVersion = $elementService->findLatestElementVersion($sourceElement);
        $sourceStructure = $elementService->findElementStructure($sourceElementVersion);

        $targetElement = $elementService->createElement(
            $sourceElementVersion->getElementSource(),
            $sourceElement->getMasterLanguage(),
            $this->getUser()->getId()
        );

        // place new element in element_tree
        $targetNode = $targetTree->create(
            $parentNode,
            $prevNode,
            $sourceNode->getTypeId(),
            $targetElement->getEid(),
            $sourceNode->getAttributes(),
            $this->getUser()->getId(),
            $sourceNode->getSortMode(),
            $sourceNode->getSortDir(),
            $sourceNode->getInNavigation()
        );

        $targetStructure = clone $sourceStructure;
        $targetStructure->setId(null);
        $targetStructure->setDataId(Uuid::generate());

        $rii = new \RecursiveIteratorIterator(new ElementStructureIterator($targetStructure->getStructures()), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $structure) {
            $structure->setId(null);
            $structure->setDataId(Uuid::generate());
        }

        $targetElementVersion = $elementService->createElementVersion(
            $targetElement,
            $targetStructure,
            null,
            $this->getUser()->getId()
        );

        return new ResultResponse(true, 'Element copied.', ['id' => $targetNode->getId()]);
    }

    /**
     * Move an Element
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/move", name="tree_move")
     */
    public function moveAction(Request $request)
    {
        $id = $request->get('id');
        $targetId = $request->get('target');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $tree = $treeManager->getByNodeId($id);
        $node = $tree->get($id);

        $targetTree = $treeManager->getByNodeId($targetId);
        $targetNode = $tree->get($targetId);

        if ($id === $targetId) {
            return new ResultResponse(false, 'source_id === target_id');
        }

        $tree->move($node, $targetNode, $this->getUser()->getId());

        return new ResultResponse(true, 'Element moved.', ['id' => $id, 'parent_id' => $targetId]);
    }

    /**
     * predelete action
     * check if element has instances
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/predelete", name="tree_delete_check")
     */
    public function checkDeleteAction(Request $request)
    {
        $treeId = $request->get('id');
        $language = $request->get('language', 'de');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $treeMeditator = $this->get('phlexible_tree.mediator');
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $nodeId = $treeId[0];
        $tree = $treeManager->getByNodeId($nodeId);
        $node = $tree->get($nodeId);

        $instances = $treeManager->getInstanceNodes($node);

        if (count($instances) > 1) {
            $instancesArray = [];
            foreach ($instances as $instanceNode) {
                $siteroot = $siterootManager->find($instanceNode->getTree()->getSiterootId());
                $instanceTitle = $treeMeditator->getField($instanceNode, 'backend', $language);

                $instancesArray[] = [
                    $instanceNode->getId(),
                    $siteroot->getTitle(),
                    $instanceTitle,
                    $instanceNode->getCreatedAt()->format('Y-m-d H:i:s'),
                    (bool) $instanceNode->getTree()->isInstanceMaster($instanceNode),
                    (bool) ($instanceNode->getId() === $nodeId)
                ];
            }

            return new ResultResponse(true, '', $instancesArray);
        }

        return new ResultResponse(true, '', []);
    }

    /**
     * Delete an Element
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/delete", name="tree_delete")
     */
    public function deleteAction(Request $request)
    {
        $treeIds = $request->get('id');
        if (!is_array($treeIds)) {
            $treeIds = [$treeIds];
        }

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $elementService = $this->get('phlexible_element.element_service');

        foreach ($treeIds as $treeId) {
            $tree = $treeManager->getByNodeId($treeId);
            $node = $tree->get($treeId);
            if (!$tree->isInstance($node)) {
                $element = $elementService->findElement($node->getTypeId());
                $elementService->deleteElement($element);
            }
            $tree->delete($node, $this->getUser()->getId());
        }

        return new ResultResponse(true, 'Item(s) deleted');
    }
}
