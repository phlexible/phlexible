<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\TeaserBundle\Doctrine\TeaserManager;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Event\ReorderTeasersEvent;
use Phlexible\Bundle\TeaserBundle\Event\TeaserEvent;
use Phlexible\Bundle\TeaserBundle\Exception\RuntimeException;
use Phlexible\Bundle\TeaserBundle\TeaserEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Layout controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/teasers/layout")
 * @Security("is_granted('ROLE_TEASERS')")
 */
class LayoutController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/tree", name="teasers_layout_tree")
     */
    public function treeAction(Request $request)
    {
        $language = $request->get('language');
        $treeId = (int) $request->get('tid');

        if (!$treeId || !$language) {
            return new Response('', 500);
        }

        $translator = $this->get('translator');
        $treeManager = $this->get('phlexible_tree.tree_manager');
        $teaserService = $this->get('phlexible_teaser.teaser_service');
        $elementService = $this->get('phlexible_element.element_service');
        $elementSourceManager = $this->get('phlexible_element.element_source_manager');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $tree = $treeManager->getByNodeId($treeId);
        $treeNode = $tree->get($treeId);
        $element = $elementService->findElement($treeNode->getTypeId());
        $elementMasterLanguage = $element->getMasterLanguage();
        $elementtype = $elementService->findElementtype($element);

        $treeNodePath = $tree->getPath($treeNode);

        $layouts = [];
        $layoutareas = [];
        // TODO: repair
        foreach ($elementSourceManager->findElementtypesByType('layout') as $layoutarea) {
            if (in_array($elementtype, $elementService->findAllowedParents($layoutarea))) {
                $layoutareas[] = $layoutarea;
            }
        }

        foreach ($layoutareas as $layoutarea) {
            $teasers = $teaserService->findForLayoutAreaAndTreeNodePath($layoutarea, $treeNodePath);
            $areaRoot = [
                'id'         => 'area_' . $layoutarea->getId(),
                'area_id'    => $layoutarea->getId(),
                'parent_tid' => $treeId,
                'parent_eid' => $element->getEid(),
                'text'       => $layoutarea->getTitle(),
                'icon'       => $iconResolver->resolveElementtype($layoutarea),
                'type'       => $layoutarea->getType(),
                'inherited'  => null, //true,
                'cls'        => 'siteroot-node',
                'leaf'       => true,
                'expanded'   => true,
                'allowDrag'  => true,
                'allowDrop'  => false,
                'children'   => [],
                'qtip'       => $translator->trans('elements.doubleclick_to_sort', [], 'gui'),
            ];

            foreach ($teasers as $teaser) {
                /* @var $teaser Teaser */
                $teaserData = [
                    'id'            => $teaser->getId(),
                    'layoutarea_id' => $layoutarea->getId(),
                    'parent_tid'    => $treeId,
                    'parent_eid'    => $element->getEid(),
                    'type'          => $teaser->getType(),
                    'inherited'     => false,
                    'inherit'       => false,
                    'leaf'          => true,
                    'hide'          => false,
                    'expanded'      => false,
                    'cls'           => '',
                    'children'      => [],
                    'allowDrag'     => false,
                    'allowDrop'     => false,
                ];

                switch ($teaser->getType()) {
                    case 'inherited':
                    case 'teaser':
                    case 'element':
                        $teaserElement = $elementService->findElement($teaser->getTypeId());
                        $teaserElementVersion = $elementService->findLatestElementVersion($teaserElement);

                        $cls = '';
                        if (!$teaser->isStopped()) {
                            $cls .= 'inherit ';
                        }
                        if ($teaser->isHidden()) {
                            $cls .= 'dont-show ';
                        }
                        if ($teaser->getTreeId() !== $treeId) {
                            $cls .= 'inherited ';
                        }

                        $teaserData = array_merge(
                            $teaserData,
                            [
                                'text'      => $teaserElementVersion->getBackendTitle($language),
                                'icon'      => $iconResolver->resolveTeaser($teaser, $language),
                                'eid'       => $teaserElement->getEid(),
                                'inherited' => $teaser->getTreeId() !== $treeId,
                                'inherit'   => !$teaser->isStopped(),
                                'cls'       => trim($cls),
                                'hide'      => $teaser->isHidden(),
                            ]
                        );
                        break;

                    default:
                        continue;
                }

                $areaRoot['children'][] = $teaserData;
            }

            if (count($areaRoot['children'])) {
                $areaRoot['leaf'] = false;
            }

            $layouts[] = $areaRoot;
        }

        return new JsonResponse($layouts);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/list", name="teasers_layout_list")
     */
    public function listAction(Request $request)
    {
        $treeId = $request->get('tid');
        $layoutAreaId = $request->get('area_id');
        $language = $request->get('language');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $treeNode = $treeManager->getByNodeId($treeId)->get($treeId);
        $treeNodePath = $treeNode->getTree()->getPath($treeNode);

        if (!$language) {
            $element = $elementService->findElement($treeNode->getTypeId());
            $language = $elementMasterLanguage = $element->getMasterLanguage();
        }

        $filter = $request->get('filter');
        if ($filter) {
            $filter = json_decode($filter, true);
        }

        $layoutarea = $elementtypeService->findElementtype($layoutAreaId);
        $teasers = $teaserManager->findForLayoutAreaAndTreeNodePath($layoutarea, $treeNodePath);

        $parent = [
            'teaser_id'       => $treeId, //(int) $teaserData->id,
            'tid'             => 0,
            'title'           => $treeNode->getId(), //$teaserData->text,
            'element_type_id' => (int) $layoutarea->getId(),
            'element_type'    => $layoutarea->getTitle(),
            'icon'            => $iconResolver->resolveElementtype($layoutarea),
            'author'          => 'author',
            'version'         => $layoutarea->getRevision(),
            'create_time'     => $layoutarea->getCreatedAt(),
            //            'change_time'     => '2007-01-01 01:01:01',
            'publish_time'    => null,
            'language'        => $language,
            'sort'            => 0,
            'sort_mode'       => 'free',
            'sort_dir'        => 'asc',
            'version_latest'  => (int) $layoutarea->getRevision(),
            'version_online'  => (int) $layoutarea->getRevision(),
            'status'          => ' o_O ',
            'qtip'            =>
                $layoutarea->getTitle() . ', Version ' . $layoutarea->getRevision() . '<br>' .
                37 . ' Versions<br>'
        ];

        $data = [];

        foreach ($teasers as $teaser) {
            /* @var $teaser Teaser */

            if ('element' == $teaser->getType()) {
                $teaserElement = $elementService->findElement($teaser->getTypeId());
                $teaserElementtype = $elementService->findElementtype($teaserElement);
                $teaserElementVersion = $elementService->findLatestElementVersion($teaserElement);

                if (!empty($filter['status'])) {
                    $status = explode(',', $filter['status']);

                    $show = false;
                    if (in_array('online', $status) && $teaserManager->isPublished($teaser, $language) && !$teaserManager->isAsync($teaser, $language)) {
                        $show = true;
                    } elseif (in_array('async', $status) && $teaserManager->isAsync($teaserElement, $language)) {
                        $show = true;
                    } elseif (in_array('offline', $status) && !$teaserManager->isPublished($teaserElement, $language)) {
                        $show = true;
                    }

                    if (!$show) {
                        continue;
                    }
                }

                $teaserOnline = $teaserManager->findOneOnlineByTeaserAndLanguage($teaser, $language);

                if (!empty($filter['date'])) {
                    $date = $filter['date'];
                    $dateFrom = !empty($filter['date_from']) ? strtotime($filter['date_from']) : '';
                    $dateTo = !empty($filter['date_to']) ? strtotime($filter['date_to']) : '';

                    $show = false;
                    if ($date === 'create') {
                        $createdAt = $teaserElementVersion->getCreatedAt();

                        if ((!$dateFrom || $createdAt > $dateFrom) && (!$dateTo || $createdAt < $dateTo)) {
                            $show = true;
                        }
                    } elseif ($date === 'publish') {
                        $publishedAt = null;
                        if ($teaserOnline) {
                            $publishedAt = $teaserOnline->getPublishedAt();
                        }

                        if ((!$dateFrom || $publishedAt > $dateFrom) && (!$dateTo || $publishedAt < $dateTo)) {
                            $show = true;
                        }
                    } elseif ($date === 'custom') {
                        $customDate = $teaserElementVersion->getCustomDate($language);

                        if ((!$dateFrom || $customDate > $dateFrom) && (!$dateTo || $customDate < $dateTo)) {
                            $show = true;
                        }
                    }

                    if (!$show) {
                        continue;
                    }
                }

                $data[] = [
                    'teaser_id'       => $teaser->getId(),
                    '_type'           => $teaser->getType(),
                    'eid'             => $teaser->getTypeId(),
                    'title'           => $teaserElementVersion->getBackendTitle($language),
                    'element_type_id' => (int) $teaserElement->getElementtypeId(),
                    'element_type'    => $teaserElementtype->getTitle(),
                    'navigation'      => 0,
                    'restricted'      => 0,
                    'icon'            => $iconResolver->resolveTeaser($teaser, $language),
                    'author'          => 'author',
                    'version'         => $teaserElementVersion->getVersion(),
                    'create_time'     => $teaserElementVersion->getCreatedAt()->format('Y-m-d H:i:s'),
                    'publish_time'    => $teaserOnline ? $teaserOnline->getPublishedAt() : '',
                    'custom_date'     => $teaserElementVersion->getCustomDate($language),
                    'language'        => $language,
                    'sort'            => (int) $teaser->getSort(),
                    'version_latest'  => (int) $teaserElement->getLatestVersion(),
                    'version_online'  => (int) $teaserManager->getPublishedVersion($teaser, $language),
                    'status'          => '>o>',
                    'qtip'            => $teaserElementVersion->getBackendTitle($language) . ', Version ' . $teaserElementVersion->getElementtypeVersion() . '<br>' .
                        'Version ' . $teaserElementVersion->getVersion() . '<br>',
                ];
            } elseif ('inherited' == $teaser->getType()) {
                $data[] = [
                    'teaser_id'       => (int) $teaser->getId(),
                    'eid'             => null,
                    '_type'           => $teaser->getType(),
                    'title'           => 'waaa', //$teaserItem->text,
                    'element_type_id' => 0,
                    'element_type'    => '',
                    'navigation'      => 0,
                    'restricted'      => 0,
                    'icon'            => $iconResolver->resolveTeaser($teaser, $language),
                    'author'          => 'author',
                    'version'         => 0,
                    'create_time'     => '',
                    'publish_time'    => null,
                    'language'        => $language,
                    'sort'            => $teaser->getSort(),
                    'version_latest'  => 0,
                    'version_online'  => 0,
                    'status'          => '>o>',
                    'qtip'            => 'waaa', //$teaserItem->text,
                ];
            }
        }

        //$data['totalChilds'] = $element->getChildCount();

        return new JsonResponse(
            [
                'parent' => $parent,
                'list'   => $data
            ]
        );
    }

    /**
     * List all element child elementtypes
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/childelementtypes", name="teasers_layout_childelementtypes")
     */
    public function childElementtypesAction(Request $request)
    {
        $id = $request->get('id');

        $elementSourceManager = $this->get('phlexible_element.element_source_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $elementtype = $elementSourceManager->findElementtype($id);
        $childElementtypes = $elementService->findAllowedChildren($elementtype);

        $data = [];
        foreach ($childElementtypes as $childElementtype) {
            $data[$childElementtype->getTitle() . $childElementtype->getId()] = [
                'id'    => $childElementtype->getId(),
                'title' => $childElementtype->getTitle(),
                'icon'  => $iconResolver->resolveElementtype($childElementtype),
            ];
        }
        ksort($data);
        $data = array_values($data);

        return new JsonResponse(['elementtypes' => $data]);
    }

    /**
     * List all child element types
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/childelements", name="teasers_layout_childelements")
     */
    public function childElementsAction(Request $request)
    {
        $tid = $request->get('tree_id');
        $layoutareaId = $request->get('layoutarea_id');
        $language = $request->get('language', 'de');

        $translator = $this->get('translator');
        $treeManager = $this->get('phlexible_tree.tree_manager');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $data = [];
        $data[] = [
            'id'    => '0',
            'title' => $translator->trans('elements.first', [], 'gui'),
            'icon'  => $iconResolver->resolveIcon('_top.gif'),
        ];

        $tree = $treeManager->getByNodeId($tid);
        $treeNode = $tree->get($tid);
        $treeNodePath = $tree->getPath($treeNode);

        $layoutarea = $elementtypeService->findElementtype($layoutareaId);
        $teasers = $teaserManager->findForLayoutAreaAndTreeNodePath($layoutarea, $treeNodePath);

        foreach ($teasers as $teaser) {
            $teaserElement = $elementService->findElement($teaser->getTypeId());
            $teaserElementVersion = $elementService->findLatestElementVersion($teaserElement);
            $data[] = [
                'id'    => $teaser->getId(),
                'title' => $teaserElementVersion->getBackendTitle($language),
                'icon'  => $iconResolver->resolveTeaser($teaser, $language),
            ];
        }

        return new JsonResponse(['elements' => $data]);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="teasers_layout_createteaser")
     */
    public function createAction(Request $request)
    {
        $treeId = $request->get('tree_id');
        $eid = $request->get('eid');
        $layoutareaId = $request->get('layoutarea_id');
        $elementtypeId = $request->get('element_type_id');
        $prevId = $request->get('prev_id', 0);
        $inherit = $request->get('inherit') == 'on' ? true : false;
        $show = $request->get('shown_here') == 'on' ? true : false;
        $masterLanguage = $request->get('masterlanguage', null);

        if (!$masterLanguage) {
            $masterLanguage = $this->container->getParameter('phlexible_cms.languages.default');
        }

        $elementService = $this->get('phlexible_element.element_service');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $elementSource = $elementService->findElementSource($elementtypeId);
        $userId = $this->getUser()->getId();

        $element = $elementService->createElement($elementSource, $masterLanguage, $userId);

        $stopIds = array();
        if (!$inherit) {
            $stopIds[] = $treeId;
        }

        $hideIds = array();
        if (!$show) {
            $hideIds[] = $treeId;
        }

        $teaser = $teaserManager->createTeaser(
            $treeId,
            $eid,
            $layoutareaId,
            'element',
            $element->getEid(),
            $prevId,
            $stopIds,
            $hideIds,
            $masterLanguage,
            $userId
        );

        return new ResultResponse(true, "Teaser with ID {$teaser->getId()} created.", ['language' => $masterLanguage]);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/createinstance", name="teasers_layout_createinstance")
     */
    public function createInstanceAction(Request $request)
    {
        $treeId = $request->get('tid');
        $layoutAreaId = $request->get('id');
        $teaserId = $request->get('for_teaser_id');

        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $treeManager = $this->get('phlexible_tree.tree_manager');

        $teaser = $teaserManager->find($teaserId);
        $treeNode = $treeManager->getByNodeId($treeId)->get($treeId);

        $teaserManager->createTeaserInstance($treeNode, $teaser, $layoutAreaId, $this->getUser()->getId());

        return new ResultResponse(true, 'Instance created.');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/delete", name="teasers_layout_delete")
     */
    public function deleteAction(Request $request)
    {
        $teaserId = $request->get('teaser_id');

        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $elementService = $this->get('phlexible_element.element_service');

        $teaser = $teaserManager->find($teaserId);
        if ($teaser->getType() === 'element') {
            $element = $elementService->findElement($teaser->getTypeId());
            $elementService->deleteElement($element);
        }

        foreach ($teaserManager->findBy(['type' => ['sort', 'stop', 'inherit'], 'typeId' => $teaser->getTypeId()]) as $subTeaser) {
            $teaserManager->deleteTeaser($subTeaser, $this->getUser()->getId());
        }

        $teaserManager->deleteTeaser($teaser, $this->getUser()->getId());

        return new ResultResponse(true, "Teaser {$teaser->getId()} deleted.");
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/inherit", name="teasers_layout_inherit")
     */
    public function inheritAction(Request $request)
    {
        $treeId = (int) $request->get('tree_id');
        $teaserId = (int) $request->get('teaser_id');

        $dispatcher = $this->get('event_dispatcher');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $teaser = $teaserManager->find($teaserId);
        $teaser->removeStopId($treeId);

        $event = new TeaserEvent($teaser);
        if ($dispatcher->dispatch(TeaserEvents::BEFORE_INHERIT_TEASER, $event)->isPropagationStopped()) {
            throw new RuntimeException('Inherit cancelled by event');
        }

        $teaserManager->updateTeaser($teaser);

        $event = new TeaserEvent($teaser);
        $dispatcher->dispatch(TeaserEvents::INHERIT_TEASER, $event);

        return new ResultResponse(true, 'Inheritance stop removed');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/stop", name="teasers_layout_stop")
     */
    public function stopAction(Request $request)
    {
        $treeId = (int) $request->get('tree_id');
        $teaserId = (int) $request->get('teaser_id');

        $dispatcher = $this->get('event_dispatcher');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $teaser = $teaserManager->find($teaserId);
        $teaser->addStopId($treeId);

        $event = new TeaserEvent($teaser);
        if ($dispatcher->dispatch(TeaserEvents::BEFORE_STOP_TEASER, $event)->isPropagationStopped()) {
            throw new RuntimeException('Stop inherit cancelled by event');
        }

        $teaserManager->updateTeaser($teaser);

        $event = new TeaserEvent($teaser);
        $dispatcher->dispatch(TeaserEvents::STOP_TEASER, $event);

        return new ResultResponse(true, 'Inheritance stopped');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/show", name="teasers_layout_show")
     */
    public function showAction(Request $request)
    {
        $treeId = (int) $request->get('tree_id');
        $teaserId = (int) $request->get('teaser_id');

        $dispatcher = $this->get('event_dispatcher');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $teaser = $teaserManager->find($teaserId);
        $teaser->removeHideId($treeId);

        $beforeEvent = new TeaserEvent($teaser);
        if ($dispatcher->dispatch(TeaserEvents::BEFORE_SHOW_TEASER, $beforeEvent)->isPropagationStopped()) {
            throw new RuntimeException('Show cancelled by event');
        }

        $teaserManager->updateTeaser($teaser);

        $event = new TeaserEvent($teaser);
        $dispatcher->dispatch(TeaserEvents::SHOW_TEASER, $event);

        return new ResultResponse(true, 'Teaser will be displayed.');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/hide", name="teasers_layout_hide")
     */
    public function hideAction(Request $request)
    {
        $treeId = (int) $request->get('tree_id');
        $teaserId = (int) $request->get('teaser_id');

        $dispatcher = $this->get('event_dispatcher');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $teaser = $teaserManager->find($teaserId);
        $teaser->addHideId($treeId);

        $beforeEvent = new TeaserEvent($teaser);
        if ($dispatcher->dispatch(TeaserEvents::BEFORE_HIDE_TEASER, $beforeEvent)->isPropagationStopped()) {
            throw new RuntimeException('Show cancelled by event');
        }

        $teaserManager->updateTeaser($teaser);

        $event = new TeaserEvent($teaser);
        $dispatcher->dispatch(TeaserEvents::HIDE_TEASER, $event);

        return new ResultResponse(true, 'Teaser will not be displayed.');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/sort", name="teasers_layout_sort")
     */
    public function sortAction(Request $request)
    {
        $treeId = $request->get('tid');
        $layoutAreaId = $request->get('area_id');
        $sortIds = $request->get('sort_ids');
        $sortIds = json_decode($sortIds, true);

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $dispatcher = $this->get('event_dispatcher');

        $tree = $treeManager->getByNodeId($treeId);
        $node = $tree->get($treeId);

        $teasers = array();
        foreach($sortIds as $sort => $teaserId) {
            $teaser = $teaserManager->find($teaserId);
            $teaser->setSort($sort);
            $teasers[] = $teaser;
        }

        $event = new ReorderTeasersEvent($node, $layoutAreaId, $teasers);
        if ($dispatcher->dispatch(TeaserEvents::REORDER_TEASERS, $event)->isPropagationStopped()) {
            throw new RuntimeException('Teaser sort cancelled by event');
        }

        $teaserManager->updateTeasers($teasers);

        $event = new ReorderTeasersEvent($node, $layoutAreaId, $teasers);
        $dispatcher->dispatch(TeaserEvents::BEFORE_REORDER_TEASERS, $event);

        return new ResultResponse(true, 'Teaser sort published.');
    }

    /**
     * Return the Element data tree
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/reference", name="teasers_layout_reference")
     */
    public function referenceAction(Request $request)
    {
        $siterootId = $request->get('siteroot_id');
        $tid = $request->get('node');
        $language = $request->get('language');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $translator = $this->get('translator');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $elementSourceManager = $this->get('phlexible_element.element_source_manager');
        $nodeSerializer = $this->get('phlexible_tree.node_serializer');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $tree = $treeManager->getBySiteRootId($siterootId);

        $treeNode = $tree->get($tid);
        $rootNode = $tree->getRoot();

        $data = [];
        if ($rootNode !== null) {
            if ($tid === null || $tid < 1) {
                $data = [$nodeSerializer->serializeNode($rootNode, $language)];

                return new JsonResponse($data);
            }

            $node = $tree->get($tid);

            // check if children of this node should be shown
            $element = $elementService->findElement($node->getTypeId());
            $elementtype = $elementService->findElementtype($element);

            $nodes = $tree->getChildren($node);
            if (!empty($nodes) && !$elementtype->getHideChildren()) {
                $data = $nodeSerializer->serializeNodes($nodes, $language);
            }

            foreach ($data as $key => $row) {
                if ($row['leaf']) {
                    unset($data[$key]);
                    continue;
                }
                $data[$key]['cls'] = (!empty($data[$key]['cls']) ? $data[$key]['cls'] . ' ' : '') . 'node-disabled';
            }
        }

        $currentTreeId = $tid;

        $element = $elementService->findElement($treeNode->getTypeId());
        $elementtype = $elementService->findElementtype($element);

        $layoutareas = [];
        // TODO: repair
        foreach ($elementSourceManager->findElementtypesByType('layout') as $layoutarea) {
            if (in_array($elementtype, $elementService->findAllowedParents($layoutarea))) {
                $layoutareas[] = $layoutarea;
            }
        }

        foreach ($layoutareas as $layoutarea) {
            $areaRoot = [
                'id'         => 'area_' . $layoutarea->getId(),
                'area_id'    => $layoutarea->getId(),
                'parent_tid' => $currentTreeId,
                'parent_eid' => $element->getEid(),
                'icon'       => $iconResolver->resolveElementtype($layoutarea),
                'text'       => $layoutarea->getTitle(),
                'type'       => 'area',
                'inherited'  => null, //true,
                'leaf'       => false,
                'expanded'   => true,
                'allowDrag'  => false,
                'allowDrop'  => false,
                'children'   => [],
                'qtip'       => $translator->trans('elements.doubleclick_to_sort', [], 'gui'),
            ];

            $teasers = $teaserManager->findForLayoutAreaAndTreeNode($layoutarea, $treeNode);

            foreach ($teasers as $teaser) {
                switch ($teaser->getType()) {
                    case 'element':
                        $teaserElement = $elementService->findElement($teaser->getTypeId());
                        $teaserElementVersion = $elementService->findLatestElementVersion($teaserElement);

                        $areaRoot['children'][] = [
                            'id'            => $teaser->getId(),
                            'parent_tid'    => $currentTreeId,
                            'parent_eid'    => $element->getEid(),
                            'layoutarea_id' => $layoutarea->getId(),
                            'icon'          => $iconResolver->resolveTeaser($teaser, $language),
                            'text'          => $teaserElementVersion->getBackendTitle($language),
                            'text'          => $teaserElementVersion->getBackendTitle($language),
                            'eid'           => $teaser->getTypeId(),
                            'type'          => 'teaser',
                            'expanded'      => false,
                            'leaf'          => true,
                            'allowDrag'     => false,
                            'allowDrop'     => false,
                            'children'      => []
                        ];

                        break;
                }
            }

            if (count($areaRoot['children'])) {
                $data[] = $areaRoot;
            }
        }

        $data = array_values($data);

        return new JsonResponse($data);
    }
}
