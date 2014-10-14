<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Event\BeforeHideInheritedTeaserEvent;
use Phlexible\Bundle\TeaserBundle\Event\BeforeStopInheritInheritedTeaserEvent;
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
 * @Security("is_granted('teasers')")
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
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $tree = $treeManager->getByNodeId($treeId);
        $treeNode = $tree->get($treeId);
        $element = $elementService->findElement($treeNode->getTypeId());
        $elementMasterLanguage = $element->getMasterLanguage();
        $elementtype = $elementtypeService->findElementtype($element->getElementtypeId());

        $treeNodePath = $tree->getPath($treeNode);

        $layouts = array();
        $layoutareas = array();
        foreach ($elementtypeService->findElementtypeByType('layout') as $layoutarea) {
            if (in_array($elementtype, $elementtypeService->findAllowedParents($layoutarea))) {
                $layoutareas[] = $layoutarea;
            }
        }

        foreach ($layoutareas as $layoutarea) {
            // TODO: switch to generic solution
            $availableLanguages = array(
                $language,
                'en',
                $elementMasterLanguage
            );

            $teasers = $teaserService->findForLayoutAreaAndTreeNodePath($layoutarea, $treeNodePath);
            // $language,
            // $availableLanguages
            // preview = true

            $areaRoot = array(
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
                'children'   => array(),
                'qtip'       => $translator->trans('elements.doubleclick_to_sort', array(), 'gui'),
            );

            foreach ($teasers as $teaser) {
                /* @var $teaser Teaser */
                $teaserData = array(
                    'id'            => $teaser->getId(),
                    'layoutarea_id' => $layoutarea->getId(),
                    'parent_tid'    => $treeId,
                    'parent_eid'    => $element->getEid(),
                    'type'          => $teaser->getType(),
                    'inherited'     => false,
                    'inherit'       => false,
                    'stop_inherit'  => false,
                    'leaf'          => true,
                    'expanded'      => false,
                    'cls'           => '',
                    'children'      => array(),
                    'allowDrag'     => false,
                    'allowDrop'     => false,
                    'no_display'    => false,
                );

                switch ($teaser->getType()) {
                    case 'catch':
                        // TODO: via events
                        $catchManager = $this->get('phlexible_element_finder.element_finder_manager');
                        $catch = $catchManager->findCatch($teaser->getTypeId());

                        $teaserData = array_merge(
                            $teaserData,
                            array(
                                'iconCls'        => 'p-teaser-catch-icon',
                                'text'        => $catch->getTitle(),
                                'leaf'        => false,
                                'expanded'    => true,
                                'allowDrag'   => true,
                                'catchConfig' => array(
                                    'id'                      => $catch->getId(),
                                    'title'                   => $catch->getTitle(),
                                    'for_tree_id'             => $catch->getTreeId(),
                                    'for_tree_id_hidden'      => $catch->getTreeId(),
                                    'catch_element_type_id'   => implode(',', $catch->getElementtypeIds()),
                                    'catch_in_navigation'     => $catch->inNavigation(),
                                    'catch_max_depth'         => $catch->getMaxDepth() >= 0
                                        ? $catch->getMaxDepth() + 1
                                        : '',
                                    'catch_max_elements'      => $catch->getMaxResults(),
                                    'catch_rotation'          => $catch->hasRotation(),
                                    'catch_pool_size'         => $catch->getPoolSize(),
                                    'catch_sort_field'        => $catch->getSortField(),
                                    'catch_sort_order'        => $catch->getSortOrder(),
                                    'catch_filter'            => $catch->getFilter(),
                                    'catch_paginator'         => $catch->getResultsPerPage() !== PHP_INT_MAX,
                                    'catch_elements_per_page' => $catch->getResultsPerPage() === PHP_INT_MAX
                                        ? ''
                                        : $catch->getResultsPerPage(),
                                ),
                            )
                        );

                        if ($catch->getMetaSearch()) {
                            $i = 1;
                            foreach ($catch->getMetaSearch() as $key => $value) {
                                $teaserData['catchConfig']['catch_meta_key_' . $i] = $key;
                                $teaserData['catchConfig']['catch_meta_keywords_' . $i] = $value;
                                $i++;
                            }
                        }

                        $catcher = $this->get('phlexible_teaser.catcher');
                        $catchResult = $catcher->catchElements($catch, $availableLanguages, true);

                        $catchItemCount = count($catchResult->getItems());
                        $textPostfix = $catchItemCount;
                        $itemsPerPage = 5;
                        if ($catchItemCount > $itemsPerPage) {
                            $textPostfix = $translator->trans('teasers.catch_showing', array($itemsPerPage, $catchItemCount), 'gui');
                        } else {
                            $textPostfix = $catchItemCount;
                        }
                        $teaserData['text'] .= ' (' . $textPostfix . ')';

                        foreach (array_slice($catchResult->getItems(), 0, $itemsPerPage) as $catchItem) {
                            $catchNode           = $treeManager->getByNodeId($catchItem['tid'])->get($catchItem['tid']);
                            $catchElement        = $elementService->findElement($catchItem['eid']);
                            $catchElementVersion = $elementService->findElementVersion($catchElement, $catchItem['version']);

                            $teaserData['children'][] = array(
                                'eid'           => $catchElement->getEid(),
                                'layoutarea_id' => $layoutarea->getId(),
                                'parent_tid'    => $treeId,
                                'parent_eid'    => $element->getEid(),
                                'icon'          => $iconResolver->resolveTreeNode($catchNode, $language),
                                'text'          => $catchElementVersion->getBackendTitle($language, $elementMasterLanguage) . ' [' . $catchNode->getId() . ']',
                                'version'       => $catchElementVersion->getVersion(),
                                'type'          => 'catched',
                                'inherited'     => false,
                                'inherit'       => false,
                                'stop_inherit'  => false,
                                'expanded'      => false,
                                'leaf'          => true,
                                'allowDrag'     => true,
                                'allowDrop'     => false,
                            );
                        }

                        break;

                    case 'inherited':
                    case 'teaser':
                    case 'element':
                        $teaserElement = $elementService->findElement($teaser->getTypeId());
                        $teaserElementVersion = $elementService->findLatestElementVersion($teaserElement);

                        $cls = '';
                        if (!$teaser->getStopInherit()) {
                            $cls .= 'inherit ';
                        }
                        if ($teaser->getNoDisplay()) {
                            $cls .= 'dont-show ';
                        }
                        if ($teaser->getTreeId() !== $treeId) {
                            $cls .= 'inherited ';
                        }

                        $teaserData = array_merge(
                            $teaserData,
                            array(
                                'text'         => $teaserElementVersion->getBackendTitle($language),
                                'icon'         => $iconResolver->resolveTeaser($teaser, $language),
                                'eid'          => $teaserElement->getEid(),
                                'inherited'    => $teaser->getTreeId() !== $treeId,
                                'inherit'      => !$teaser->getStopInherit(),
                                'stop_inherit' => false,
                                'cls'          => trim($cls),
                                'no_display'   => $teaser->getNoDisplay() ? true : false,
                            )
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

        $parent = array(
            'teaser_id'       => $treeId, //(int) $teaserData->id,
            'tid'             => 0,
            'title'           => $treeNode->getId(), //$teaserData->text,
            'element_type_id' => (int) $layoutarea->getId(),
            'element_type'    => $layoutarea->getTitle(),
            'icon'            => $iconResolver->resolveElementtype($layoutarea),
            'author'          => 'author',
            'version'         => $layoutarea->getLatestVersion(),
            'create_time'     => $layoutarea->getCreatedAt(),
            //            'change_time'     => '2007-01-01 01:01:01',
            'publish_time'    => null,
            'language'        => $language,
            'sort'            => 0,
            'sort_mode'       => 'free',
            'sort_dir'        => 'asc',
            'version_latest'  => (int) $layoutarea->getLatestVersion(),
            'version_online'  => (int) $layoutarea->getLatestVersion(),
            'status'          => ' o_O ',
            'qtip'            =>
                $layoutarea->getTitle() . ', Version ' . $layoutarea->getLatestVersion() . '<br>' .
                37 . ' Versions<br>'
        );

        $data = array();

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

                $data[] = array(
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
                    //                'change_time'     => $child['modify_time'],
                    'publish_time'    => $teaserOnline ? $teaserOnline->getPublishedAt() : '',
                    'custom_date'     => $teaserElementVersion->getCustomDate($language),
                    'language'        => $language,
                    'sort'            => (int) $teaser->getSort(),
                    'version_latest'  => (int) $teaserElement->getLatestVersion(),
                    'version_online'  => (int) $teaserManager->getPublishedVersion($teaser, $language),
                    'status'          => '>o>',
                    'qtip'            => $teaserElementVersion->getBackendTitle($language) . ', Version ' . $teaserElementVersion->getElementtypeVersion() . '<br>' .
                        'Version ' . $teaserElementVersion->getVersion() . '<br>',
                );
            } elseif ('catch' === $teaser->getType()) {
                $catch = $this->get('phlexible_teaser.element_finder_manager')->findCatch($teaser->getTypeId());

                $data[] = array(
                    'teaser_id'       => (int) $teaser->getId(),
                    'eid'             => null,
                    '_type'           => $teaser->getType(),
                    'title'           => $catch->getTitle(),
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
                    'qtip'            => $catch->getTitle(),
                );
            } elseif ('inherited' == $teaser->getType()) {
                $data[] = array(
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
                    //                'change_time'     => $child['modify_time'],
                    'publish_time'    => null,
                    'language'        => $language,
                    'sort'            => $teaser->getSort(),
                    'version_latest'  => 0,
                    'version_online'  => 0,
                    'status'          => '>o>',
                    'qtip'            => 'waaa', //$teaserItem->text,
                );
            }
        }

        //$data['totalChilds'] = $element->getChildCount();

        return new JsonResponse(
            array(
                'parent' => $parent,
                'list'   => $data
            )
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
        $defaultLanguage = $this->container->getParameter('phlexible_cms.languages.default');

        $id = $request->get('id');

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $elementtype = $elementtypeService->findElementtype($id);
        $childElementtypes = $elementtypeService->findAllowedChildren($elementtype);

        $data = array();
        foreach ($childElementtypes as $childElementtype) {
            $data[$childElementtype->getTitle() . $childElementtype->getId()] = array(
                'id'    => $childElementtype->getId(),
                'title' => $childElementtype->getTitle(),
                'icon'  => $iconResolver->resolveElementtype($childElementtype),
            );
        }
        ksort($data);
        $data = array_values($data);

        return new JsonResponse(array('elementtypes' => $data));
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

        $data = array();
        $data[] = array(
            'id'    => '0',
            'title' => $translator->trans('elements.first', array(), 'gui'),
            'icon'  => $iconResolver->resolveIcon('_top.gif'),
        );

        $tree = $treeManager->getByNodeId($tid);
        $treeNode = $tree->get($tid);
        $treeNodePath = $tree->getPath($treeNode);

        $layoutarea = $elementtypeService->findElementtype($layoutareaId);
        $teasers = $teaserManager->findForLayoutAreaAndTreeNodePath($layoutarea, $treeNodePath);

        foreach ($teasers as $teaser) {
            $teaserElement = $elementService->findElement($teaser->getTypeId());
            $teaserElementVersion = $elementService->findLatestElementVersion($teaserElement);
            $data[] = array(
                'id'    => $teaser->getId(),
                'title' => $teaserElementVersion->getBackendTitle($language),
                'icon'  => $iconResolver->resolveTeaser($teaser, $language),
            );
        }

        return new JsonResponse(array('elements' => $data));
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="teasers_layout_createteaser")
     */
    public function createAction(Request $request)
    {
        $siterootId = $request->get('siteroot_id');
        $treeId = $request->get('tree_id');
        $eid = $request->get('eid');
        $layoutareaId = $request->get('layoutarea_id');
        $elementtypeId = $request->get('element_type_id');
        $prevId = $request->get('prev_id', 0);
        $inherit = $request->get('inherit') == 'on' ? true : false;
        $noDisplay = $request->get('no_display') == 'on' ? true : false;
        $masterLanguage = $request->get('masterlanguage', null);

        if (!$masterLanguage) {
            $masterLanguage = $this->container->getParameter('phlexible_cms.languages.default');
        }

        $elementService = $this->get('phlexible_element.element_service');
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $elementtype = $elementtypeService->findElementtype($elementtypeId);

        $userId = $this->getUser()->getId();

        $element = $elementService->createElement($elementtype, $masterLanguage, $userId);

        $teaser = $teaserManager->createTeaser(
            $treeId,
            $eid,
            $layoutareaId,
            'element',
            $element->getEid(),
            $prevId,
            $inherit,
            $noDisplay,
            $masterLanguage,
            $userId
        );

        return new ResultResponse(true, "Teaser with ID {$teaser->getId()} created.", array('language' => $masterLanguage));
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
        $type = $request->get('type');

        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $elementService = $this->get('phlexible_element.element_service');

        $teaser = $teaserManager->find($teaserId);
        if ($teaser->getType() === 'element') {
            $element = $elementService->findElement($teaser->getTypeId());
            $elementService->deleteElement($element);
        }

        foreach ($teaserManager->findBy(array('type' => array('sort', 'stop', 'inherit'), 'typeId' => $teaser->getTypeId())) as $subTeaser) {
            $teaserManager->deleteTeaser($subTeaser, $this->getUser()->getId());
        }

        $teaserManager->deleteTeaser($teaser, $this->getUser()->getId());

        // TODO: fix
        /*
        $job = new Makeweb_Elements_Job_UpdateUsage();
        $job->setEid($eid);
        */

        return new ResultResponse(true, "Teaser {$teaser->getId()} deleted.");
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/inherit", name="teasers_layout_inherit")
     */
    public function toggleInheritAction(Request $request)
    {
        $teaserId = $request->get('teaser_id');

        $dispatcher = $this->get('event_dispatcher');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $teaser = $teaserManager->find($teaserId);

        if ($teaser->getStopInherit()) {
            $event = new TeaserEvent($teaser);
            if ($dispatcher->dispatch(TeaserEvents::BEFORE_INHERIT_TEASER, $event)->isPropagationStopped()) {
                throw new RuntimeException('Toggle inherit stopped.');
            }

            $teaser->setStopInherit(false);

            $teaserManager->updateTeaser($teaser);

            $event = new TeaserEvent($teaser);
            $dispatcher->dispatch(TeaserEvents::BEFORE_INHERIT_TEASER, $event);

            $msg = 'Inheritance stopped.';
        } else {
            $event = new TeaserEvent($teaser);
            if ($dispatcher->dispatch(TeaserEvents::BEFORE_STOP_INHERIT_TEASER, $event)->isPropagationStopped()) {
                throw new RuntimeException('Toggle inherit stopped.');
            }

            $teaser->setStopInherit(true);

            $teaserManager->updateTeaser($teaser);

            $event = new TeaserEvent($teaser);
            $dispatcher->dispatch(TeaserEvents::BEFORE_STOP_INHERIT_TEASER, $event);

            $msg = 'Inheritance stop removed.';
        }

        return new ResultResponse(true, $msg);
    }

    /**
     * @param Request $request
     *
     * @Route("/inheritinherited", name="teasers_layout_inheritinherited")
     */
    public function toggleInheritInheritedAction(Request $request)
    {
        $layoutAreaId = $request->get('layoutarea_id');
        $treeId = $request->get('tree_id');
        $eid = $request->get('eid');
        $teaserEid = $request->get('teaser_eid');
        $teaserId = $request->get('teaser_id');

        $dispatcher = $this->get('event_dispatcher');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $stopTeaser = $teaserManager->findOneBy(
            array(
                'type'   => 'stop',
                'treeId' => $treeId,
                'eid'    => $eid,
                'typeId' => $teaserId
            )
        );

        if ($stopTeaser) {
            $event = new TeaserEvent($stopTeaser);
            if ($dispatcher->dispatch(TeaserEvents::BEFORE_INHERIT_INHERITED_TEASER, $event)->isPropagationStopped()) {
                throw new RuntimeException('Stop inherit cancelled by event');
            }

            $teaserManager->deleteTeaser($stopTeaser, $this->getUser()->getId());

            $msg = 'Inheritance stop removed';

            $event = new TeaserEvent($stopTeaser);
            $dispatcher->dispatch(TeaserEvents::INHERIT_INHERITED_TEASER, $event);
        } else {
            $event = new BeforeStopInheritInheritedTeaserEvent($treeId, $eid, $teaserId, $layoutAreaId);
            if ($dispatcher->dispatch(TeaserEvents::BEFORE_STOP_INHERIT_INHERITED_TEASER, $event)->isPropagationStopped()) {
                throw new RuntimeException('Stop inherit cancelled by event');
            }

            $stopTeaser = $teaserManager->createTeaser(
                $treeId,
                $eid,
                $layoutAreaId,
                'stop',
                $teaserId,
                0,
                false,
                false,
                'en',
                $this->getUser()->getId()
            );

            $msg = 'Inheritance stopped';

            $event = new TeaserEvent($stopTeaser);
            $dispatcher->dispatch(TeaserEvents::STOP_INHERIT_INHERITED_TEASER, $event);
        }


        return new ResultResponse(true, $msg);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/show", name="teasers_layout_show")
     */
    public function showAction(Request $request)
    {
        $teaserId = (int) $request->get('teaser_id');

        $dispatcher = $this->get('event_dispatcher');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $teaser = $teaserManager->find($teaserId);
        $teaser->setNoDisplay(false);

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
        $teaserId = (int) $request->get('teaser_id');

        $dispatcher = $this->get('event_dispatcher');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $teaser = $teaserManager->find($teaserId);
        $teaser->setNoDisplay(true);

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
     * @Route("/showinherited", name="teasers_layout_showinherited")
     */
    public function showInheritedAction(Request $request)
    {
        $layoutAreaId = (int) $request->get('layoutarea_id');
        $treeId = (int) $request->get('tree_id');
        $eid = (int) $request->get('eid');
        $teaserEid = (int) $request->get('teaser_eid');
        $teaserId = (int) $request->get('teaser_id');

        $dispatcher = $this->get('event_dispatcher');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $hideTeaser = $teaserManager->findOneBy(
            array(
                'treeId'       => $treeId,
                'layoutareaId' => $layoutAreaId,
                'type'         => 'hide',
                'typeId'       => $teaserId
            )
        );

        if ($hideTeaser) {
            $event = new TeaserEvent($hideTeaser);
            if ($dispatcher->dispatch(TeaserEvents::BEFORE_SHOW_INHERITED_TEASER, $event)->isPropagationStopped()) {
                throw new RuntimeException('Show cancelled by event');
            }

            $teaserManager->deleteTeaser($hideTeaser, $this->getUser()->getId());

            $event = new TeaserEvent($hideTeaser);
            $dispatcher->dispatch($event);
        }

        return new ResultResponse(true, 'Teaser will be displayed.');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/hideinherited", name="teasers_layout_hideinherited")
     */
    public function hideInheritedAction(Request $request)
    {
        $layoutAreaId = (int) $request->get('layoutarea_id');
        $treeId = (int) $request->get('tree_id');
        $eid = (int) $request->get('eid');
        $teaserEid = (int) $request->get('teaser_eid');
        $teaserId = (int) $request->get('teaser_id');

        $dispatcher = $this->get('event_dispatcher');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $hideTeaser = $teaserManager->findOneBy(
            array(
                'treeId' => $treeId,
                'type'   => 'hide',
                'typeId' => $teaserId
            )
        );

        if (!$hideTeaser) {
            $event = new BeforeHideInheritedTeaserEvent($treeId, $eid, $teaserId, $layoutAreaId);
            if ($dispatcher->dispatch(TeaserEvents::BEFORE_HIDE_INHERITED_TEASER, $event)->isPropagationStopped()) {
                throw new RuntimeException('Hide cancelled by event');
            }

            $hideTeaser = $teaserManager->createTeaser(
                $treeId,
                $eid,
                $layoutAreaId,
                'hide',
                $teaserId,
                0,
                false,
                true,
                'en',
                $this->getUser()->getId()
            );

            $event = new TeaserEvent($hideTeaser);
            $dispatcher->dispatch(TeaserEvents::HIDE_INHERITED_TEASER, $event);
        }

        return new ResultResponse(true, 'Teaser will not be displayed.');
    }

    /**
     * @Route("/sort", name="teasers_layout_sort")
     */
    public function sortAction()
    {
        // TODO use Brainbits_Filter_Input
        $treeId = $this->_getParam('tid');
        $eid = $this->_getParam('eid');
        $layoutAreaId = $this->_getParam('area_id');
        $sortIds = $this->_getParam('sort_ids');
        $sortIds = json_decode($sortIds, true);

        $dispatcher = $this->getContainer()->get('event_dispatcher');

        try {
            $beforeEvent = new Makeweb_Teasers_Event_BeforeReorderTeasers($treeId, $eid, $layoutAreaId, $sortIds);
            if (false === $dispatcher->dispatch($beforeEvent)) {
                throw new RuntimeException('Teaser sort cancelled by event');
            }

            $db = $this->getContainer()->dbPool->default;

            $db->beginTransaction();

            $select = $db->select()
                ->from($db->prefix . 'element_tree_teasers', 'layoutarea_id')
                ->where('id = :teaserId');

            foreach ($sortIds as $sort => $teaserId) {
                if (!$teaserId) {
                    continue;
                }

                if (-1 == $teaserId) {
                    $insertData = array(
                        'tree_id'       => $treeId,
                        'eid'           => $eid,
                        'layoutarea_id' => $layoutAreaId,
                        'teaser_eid'    => null,
                        'type'          => Makeweb_Teasers_Manager::TYPE_INHERITED,
                        'sort'          => $sort,
                        'modify_uid'    => MWF_Env::getUid(),
                        'modify_time'   => $db->fn->now(),
                    );

                    $db->insert($db->prefix . 'element_tree_teasers', $insertData);

                    $teaserId = $db->lastInsertId($db->prefix . 'element_tree_teasers');

                    continue;
                }

                $exists = $db->fetchOne($select, array('teaserId' => $teaserId)) ? true : false;

                if (!$exists) {
                    continue;
                }

                $db->update(
                    $db->prefix . 'element_tree_teasers',
                    array('sort' => $sort),
                    array('id = ?' => $teaserId)
                );
            }

            $db->commit();
            $event = new Makeweb_Teasers_Event_ReorderTeasers($treeId, $eid, $layoutAreaId, $sortIds);
            $dispatcher->dispatch($event);

            $result = MWF_Ext_Result::encode(true, null, 'Teaser sort published.');
        } catch (Exception $e) {
            $db->rollback();

            $result = MWF_Ext_Result::encode(false, null, $e->getMessage());
        }

        $this->getResponse()->setAjaxPayload($result);
    }
}
