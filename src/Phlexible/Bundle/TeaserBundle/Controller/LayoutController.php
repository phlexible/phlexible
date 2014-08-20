<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Controller;

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
        $treeId = $request->get('tid');

        if (!$treeId || !$language) {
            return new Response('', 500);
        }

        $translator = $this->get('translator');
        $treeManager = $this->get('phlexible_tree.tree_manager');
        $teaserService = $this->get('phlexible_teaser.teaser_service');
        $catchManager = $this->get('phlexible_teaser.catch_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $elementtypeService = $elementService->getElementtypeService();
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
            if (in_array($elementtype->getId(), $elementtypeService->findAllowedParentIds($layoutarea))) {
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
                            $textPostfix = $translator->trans('teaser.catch_showing', array($itemsPerPage, $catchItemCount), 'gui');
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

                        $teaserData += array(
                            'text'         => $teaserElementVersion->getBackendTitle($language),
                            'icon'         => $iconResolver->resolveTeaser($teaser, $language),
                            'eid'          => $teaserElement->getEid(),
                            'type'         => 'teaser',
                            'inherited'    => $teaser->getTreeId() !== $treeId,
                            'inherit'      => !$teaser->getStopInherit(),
                            'stop_inherit' => false,
                            'cls'          => trim($cls),
                            'no_display'   => $teaser->getNoDisplay() ? true : false,
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
     * List all Elements
     *
     * @Route("/list", name="teasers_layout_list")
     */
    public function listAction()
    {
        $container = $this->getContainer();

        $treeManager = $container->get('phlexible_tree.tree_manager');
        $teaserManager = $container->get('teasersManager');
        $elementManager = $container->get('elementsManager');
        $elementVersionManager = $container->get('elementsVersionManager');
        $elementTypeManager = Makeweb_Elementtypes_Elementtype_Manager::getInstance();
        $layoutAreaManager = Makeweb_Teasers_Layoutarea_Manager::getInstance();
        $elementTypeVersionManager = Makeweb_Elementtypes_Elementtype_Version_Manager::getInstance();
        $layoutAreaManager = Makeweb_Teasers_Layoutarea_Manager::getInstance();

        $data = array();

        $tid = $this->_getParam('tid');
        $node = $treeManager->getNodeByNodeId($tid);
        $treePath = $node->getTree()->getNodePath($tid);
        $element = $elementManager->getByEID($node->getEID());
        $elementMasterLanguage = $element->getMasterLanguage();
        $layoutAreaId = $this->_getParam('area_id');
        $language = $this->_getParam('language', $elementMasterLanguage);

        $filter = $this->_getParam('filter');
        if ($filter) {
            $filter = json_decode($filter, true);
        }

        $layoutArea = $elementTypeVersionManager->getLatest($layoutAreaId);
        $teaserData = (object) $teaserManager->getAllByTIDPath($treePath, $layoutArea, $language, array(), true);

        $parent = array(
            'teaser_id'       => (int) $teaserData->id,
            'tid'             => 0,
            'title'           => $teaserData->text,
            'element_type_id' => (int) $teaserData->layoutareaId,
            'element_type'    => $layoutArea->getTitle(),
            'icon'            => $layoutArea->getIconUrl(),
            'author'          => 'author',
            'version'         => $layoutArea->getVersion(),
            'create_time'     => $layoutArea->getModifyTime(),
            //            'change_time'     => '2007-01-01 01:01:01',
            'publish_time'    => null,
            'language'        => $language,
            'sort'            => 0,
            'sort_mode'       => 'free',
            'sort_dir'        => 'asc',
            'version_latest'  => (int) $layoutArea->getVersion(),
            'version_online'  => (int) $layoutArea->getVersion(),
            'status'          => ' o_O ',
            'qtip'            =>
                $layoutArea->getTitle() . ', Version ' . $layoutArea->getVersion() . '<br>' .
                37 . ' Versions<br>'
        );

        $data = array();

        foreach ($teaserData->children as $teaserItem) {
            $teaserItem = (object) $teaserItem;

            if (Makeweb_Teasers_Manager::TYPE_TEASER == $teaserItem->type) {
                try {
                    $teaserElementVersion = $elementVersionManager->getLatest($teaserItem->eid);
                    $teaserElementTypeVersion = $teaserElementVersion->getElementTypeVersionObj();
                } catch (Exception $e) {
                    continue;
                }

                $teaserNode = $teaserItem->node;

                if (!empty($filter['status'])) {
                    $status = explode(',', $filter['status']);

                    $show = false;
                    if (in_array('online', $status) && $teaserNode->isPublished($language) && !$teaserNode->isAsync(
                            $language
                        )
                    ) {
                        $show = true;
                    } elseif (in_array('async', $status) && $teaserNode->isAsync($language)) {
                        $show = true;
                    } elseif (in_array('offline', $status) && !$teaserNode->isPublished($language)) {
                        $show = true;
                    }

                    if (!$show) {
                        continue;
                    }
                }

                if (!empty($filter['date'])) {
                    $date = $filter['date'];
                    $dateFrom = !empty($filter['date_from']) ? strtotime($filter['date_from']) : '';
                    $dateTo = !empty($filter['date_to']) ? strtotime($filter['date_to']) : '';

                    $show = false;
                    if ($date === 'create') {
                        $createDate = strtotime($teaserElementVersion->getCreateTime());

                        if ((!$dateFrom || $createDate > $dateFrom) && (!$dateTo || $createDate < $dateTo)) {
                            $show = true;
                        }
                    } elseif ($date === 'publish') {
                        $publishDate = strtotime($teaserNode->getPublishTime($language));

                        if ((!$dateFrom || $publishDate > $dateFrom) && (!$dateTo || $publishDate < $dateTo)) {
                            $show = true;
                        }
                    } elseif ($date === 'custom') {
                        $customDate = strtotime($teaserElementVersion->getCustomDate($language));

                        if ((!$dateFrom || $customDate > $dateFrom) && (!$dateTo || $customDate < $dateTo)) {
                            $show = true;
                        }
                    }

                    if (!$show) {
                        continue;
                    }
                }

                $data[] = array(
                    'teaser_id'       => (int) $teaserItem->id,
                    'eid'             => (int) $teaserItem->eid,
                    '_type'           => 'teaser',
                    'title'           => $teaserItem->text,
                    'element_type_id' => (int) $teaserElementTypeVersion->getID(),
                    'element_type'    => $teaserElementTypeVersion->getTitle(),
                    'navigation'      => 0,
                    'restricted'      => 0,
                    'icon'            => $teaserElementVersion->getIconUrl($teaserNode->getIconParams($language)),
                    'author'          => 'author',
                    'version'         => $teaserElementVersion->getVersion(),
                    'create_time'     => $teaserElementVersion->getCreateTime(),
                    //                'change_time'     => $child['modify_time'],
                    'publish_time'    => $teaserNode->getPublishTime($language),
                    'custom_date'     => $teaserElementVersion->getCustomDate($language),
                    'language'        => $language,
                    'sort'            => (int) $teaserNode->getSort(),
                    'version_latest'  => (int) $teaserNode->getLatestVersion(),
                    'version_online'  => (int) $teaserNode->getOnlineVersion($language),
                    'status'          => '>o>',
                    'qtip'            => $teaserElementTypeVersion->getTitle(
                        ) . ', Version ' . $teaserElementTypeVersion->getVersion() . '<br>' .
                        'Version ' . $teaserElementVersion->getVersion() . '<br>',
                );
            } elseif (Makeweb_Teasers_Manager::TYPE_CATCH == $teaserItem->type) {
                $data[] = array(
                    'teaser_id'       => (int) $teaserItem->id,
                    'eid'             => null,
                    '_type'           => $teaserItem->type,
                    'title'           => $teaserItem->text,
                    'element_type_id' => 0,
                    'element_type'    => '',
                    'navigation'      => 0,
                    'restricted'      => 0,
                    'icon'            => $this->_request->getBaseUrl(
                        ) . '/resources/asset/elementtypes/elementtypes/_left.gif',
                    'author'          => 'author',
                    'version'         => 0,
                    'create_time'     => '',
                    //                'change_time'     => $child['modify_time'],
                    'publish_time'    => null,
                    'language'        => $language,
                    'sort'            => $teaserItem->sort,
                    'version_latest'  => 0,
                    'version_online'  => 0,
                    'status'          => '>o>',
                    'qtip'            => $teaserItem->text,
                );
            } elseif (Makeweb_Teasers_Manager::TYPE_INHERITED == $teaserItem->type) {
                $data[] = array(
                    'teaser_id'       => (int) $teaserItem->id,
                    'eid'             => null,
                    '_type'           => $teaserItem->type,
                    'title'           => $teaserItem->text,
                    'element_type_id' => 0,
                    'element_type'    => '',
                    'navigation'      => 0,
                    'restricted'      => 0,
                    'icon'            => $this->_request->getBaseUrl(
                        ) . '/resources/asset/elementtypes/elementtypes/_up.gif',
                    'author'          => 'author',
                    'version'         => 0,
                    'create_time'     => '',
                    //                'change_time'     => $child['modify_time'],
                    'publish_time'    => null,
                    'language'        => $language,
                    'sort'            => $teaserItem->sort,
                    'version_latest'  => 0,
                    'version_online'  => 0,
                    'status'          => '>o>',
                    'qtip'            => $teaserItem->text,
                );
            }
        }

        //$data['totalChilds'] = $element->getChildCount();

        $this->getResponse()->setAjaxPayload(
            array(
                'parent' => $parent,
                'list'   => $data
            )
        );
    }

    /**
     * List all element child elementtypes
     *
     * @Route("/childelementtypes", name="teasers_layout_childelementtypes")
     */
    public function childelementtypesAction()
    {
        $container = $this->getContainer();

        $defaultLanguage = $container->getParameter('phlexible_cms.languages.default');

        $elementTypeVersionManager = Makeweb_Elementtypes_Elementtype_Version_Manager::getInstance();

        // TODO use Brainbits_Filter_Input
        $id = $this->_getParam('id');
        $language = $this->_getParam('language', $defaultLanguage);

        //        $elementTypeVersionManager = Makeweb_Elementtypes_Elementtype_Version_Manager::getInstance();
        $elementTypeVersion = $elementTypeVersionManager->getLatest($id, $language);
        $children = $elementTypeVersion->getAllowedChildren();

        $data = array();
        foreach ($children as $childId => $child) {
            /* @var $child Makeweb_Elementtypes_Elementtype */

            $data[$child->getTitle() . $childId] = array(
                'id'    => $childId,
                'title' => $child->getTitle(),
                'icon'  => $this->_request->getBaseUrl() . '/elements/asset/' . $child->getIcon(),
            );
        }
        ksort($data);
        $data = array_values($data);

        $this->getResponse()->setAjaxPayload(array('elementtypes' => $data));
    }

    /**
     * List all child element types
     *
     * @Route("/childelements", name="teasers_layout_childelements")
     */
    public function childelementsAction()
    {
        $container = $this->getContainer();
        $translator = $container->get('translator');
        $treeManager = $container->get('phlexible_tree.tree_manager');
        $teaserManager = $container->teasersManager;
        $elementTypeVersionManager = $container->elementtypesVersionManager;

        $tid = $this->_getParam('tree_id');
        $layoutAreaId = $this->_getParam('layoutarea_id');
        $language = 'de';

        $data = array();
        $data[] = array(
            'id'    => '0',
            'title' => $translator->trans('elements.first', array(), 'gui'),
            'icon'  => $this->_request->getBaseUrl() . '/elements/asset/_top.gif'
        );

        $node = $treeManager->getNodeByNodeId($tid);
        $treePath = $node->getTree()->getNodePath($tid);

        $layoutArea = $elementTypeVersionManager->getLatest($layoutAreaId);
        $teaserData = (object) $teaserManager->getAllByTIDPath($treePath, $layoutArea, $language, array(), true);

        foreach ($teaserData->children as $pos => $teaserItem) {
            $teaserItem = (object) $teaserItem;

            $data[] = array(
                'id'    => $teaserItem->id,
                'title' => $teaserItem->text,
                'icon'  => $teaserItem->icon,
            );
        }

        $this->getResponse()->setAjaxPayload(array('elements' => $data));
    }

    /**
     * @Route("/createteaser", name="teasers_layout_createteaser")
     */
    public function createteaserAction()
    {
        // TODO use Brainbits_Filter_Input
        $siterootId = $this->_getParam('siteroot_id');
        $treeId = $this->_getParam('tree_id');
        $eid = $this->_getParam('eid');
        $layoutareaId = $this->_getParam('layoutarea_id');
        $newElementTypeID = $this->_getParam('element_type_id');
        $prevId = $this->_getParam('prev_id', 0);
        $inherit = $this->_getParam('inherit') == 'on' ? true : false;
        $noDisplay = $this->_getParam('no_display') == 'on' ? true : false;
        $masterLanguage = $this->_getParam('masterlanguage', null);

        if (!$masterLanguage) {
            $container = $this->getContainer();
            $masterLanguage = $container->getParameter('frontend.languages.frontend');
        }

        try {
            $manager = Makeweb_Teasers_Manager::getInstance();
            $node = $manager->createTeaser(
                $treeId,
                $eid,
                $layoutareaId,
                $newElementTypeID,
                $prevId,
                $inherit,
                $noDisplay,
                $masterLanguage
            );

            Makeweb_Teasers_History::insert(
                Makeweb_Teasers_History::ACTION_CREATE_TEASER,
                $node->getId(),
                $node->getEid()
            );

            $result = MWF_Ext_Result::encode(
                true,
                $node->getId(),
                'Teaser with ID "' . $node->getId() . '" created.',
                array('language' => $masterLanguage)
            );
        } catch (Exception $e) {
            $result = MWF_Ext_Result::encode(false, null, $e->getMessage());
        }

        $this->getResponse()->setAjaxPayload($result);
    }

    /**
     * @Route("/createinstance", name="teasers_layout_createinstance")
     */
    public function createinstanceAction()
    {
        try {
            $treeId = $this->_getParam('tid');
            $layoutAreaId = $this->_getParam('id');
            $teaserId = $this->_getParam('for_teaser_id');

            $manager = Makeweb_Teasers_Manager::getInstance();
            $manager->createTeaserInstance($treeId, $teaserId, $layoutAreaId);

            $result = MWF_Ext_Result::encode(true, null, 'Instance created.');
        } catch (Exception $e) {
            $result = MWF_Ext_Result::encode(false, null, $e->getMessage());
        }

        $this->getResponse()->setAjaxPayload($result);
    }

    /**
     * @Route("/delete", name="teasers_layout_delete")
     */
    public function deleteAction()
    {
        $teaserId = $this->_getParam('teaser_id');
        $type = $this->_getParam('type');

        $manager = Makeweb_Teasers_Manager::getInstance();

        try {
            switch ($type) {
                case 'teaser':
                    $db = $this->getContainer()->dbPool->default;
                    $select = $db->select()->from($db->prefix . 'element_tree_teasers', 'teaser_eid')->where(
                        'id = ?',
                        $teaserId
                    );
                    $eid = $db->fetchOne($select);

                    $manager->deleteTeaser($teaserId);

                    $queueService = $this->getContainer()->get('queue.service');
                    $job = new Makeweb_Elements_Job_UpdateUsage();
                    $job->setEid($eid);
                    $queueService->addUniqueJob($job);
                    //$fileUsage = new Makeweb_Elements_Element_FileUsage(MWF_Registry::getContainer()->dbPool);
                    //$fileUsage->update($eid);

                    $result = MWF_Ext_Result::encode(true, $teaserId, 'Teaser ID "' . $teaserId . '" deleted.');
                    break;

                case 'catch':
                    $manager->deleteCatch($teaserId);
                    $result = MWF_Ext_Result::encode(true, $teaserId, 'Catch deleted.');
                    break;

                default:
                    $result = MWF_Ext_Result::encode(false, null, 'No Teaser ID given.');
                    break;
            }
        } catch (Exception $e) {
            $result = MWF_Ext_Result::encode(false, null, $e->getMessage());
        }

        $this->getResponse()->setAjaxPayload($result);
    }

    /**
     * @Route("/inherit", name="teasers_layout_inherit")
     */
    public function inheritAction()
    {
        // TODO use Brainbits_Filter_Input
        $teaserId = $this->_getParam('teaser_id');

        try {
            $container = $this->getContainer();
            $dispatcher = $container->get('event_dispatcher');
            $db = $container->dbPool->write;

            $select = $db->select()
                ->from($db->prefix . 'element_tree_teasers', 'stop_inherit')
                ->where('id = ?', $teaserId);

            $inherit = $db->fetchOne($select);

            $node = new Makeweb_Teasers_Node($teaserId);

            if (!$inherit) {
                $beforeEvent = new Makeweb_Teasers_Event_BeforeStopInheritTeaser($node);
                if (false === $dispatcher->dispatch($beforeEvent)) {
                    throw new Exception('Stop inherit cancelled by event');
                }
            } else {
                $beforeEvent = new Makeweb_Teasers_Event_BeforeInheritTeaser($node);
                if (false === $dispatcher->dispatch($beforeEvent)) {
                    throw new Exception('Inherit cancelled by event');
                }
            }

            $db->update(
                $db->prefix . 'element_tree_teasers',
                array('stop_inherit' => new Zend_Db_Expr('!stop_inherit')),
                array('id = ?' => $teaserId)
            );

            if (!$inherit) {
                $event = new Makeweb_Teasers_Event_StopInheritTeaser($node);
                $dispatcher->dispatch($event);

                $result = MWF_Ext_Result::encode(true, null, 'Inheritance stopped.');
            } else {
                $event = new Makeweb_Teasers_Event_InheritTeaser($node);
                $dispatcher->dispatch($event);

                $result = MWF_Ext_Result::encode(true, null, 'Inheritance stop removed.');
            }
        } catch (Exception $e) {
            $result = MWF_Ext_Result::encode(false, null, $e->getMessage());
        }

        $this->getResponse()->setAjaxPayload($result);
    }

    /**
     * @Route("/inheritinherited", name="teasers_layout_inheritinherited")
     */
    public function inheritinheritedAction()
    {
        // TODO use Brainbits_Filter_Input
        $layoutAreaId = $this->_getParam('layoutarea_id');
        $treeId = $this->_getParam('tree_id');
        $eid = $this->_getParam('eid');
        $teaserEid = $this->_getParam('teaser_eid');

        try {
            $dispatcher = $this->getContainer()->get('event_dispatcher');

            $db = $this->getContainer()->dbPool->default;

            $select = $db->select()
                ->from($db->prefix . 'element_tree_teasers', 'id')
                ->where('type = ?', 'stop')
                ->where('tree_id = ?', $treeId)
                ->where('eid = ?', $eid)
                ->where('teaser_eid = ?', $teaserEid);

            $stopInheritId = $db->fetchOne($select);

            if ($stopInheritId) {
                $beforeEvent = new Makeweb_Teasers_Event_BeforeInheritInheritedTeaser($treeId, $eid, $teaserEid, $layoutAreaId);
                if (false === $dispatcher->dispatch($beforeEvent)) {
                    throw new Exception('Stop inherit cancelled by event');
                }

                $db->delete($db->prefix . 'element_tree_teasers', array('id = ?' => $stopInheritId));

                $msg = 'Inheritance stop removed';

                $event = new Makeweb_Teasers_Event_InheritInheritedTeaser($treeId, $eid, $teaserEid, $layoutAreaId, $stopInheritId);
                $dispatcher->dispatch($event);
            } else {
                $beforeEvent = new Makeweb_Teasers_Event_BeforeStopInheritInheritedTeaser($treeId, $eid, $teaserEid, $layoutAreaId);
                if (false === $dispatcher->dispatch($beforeEvent)) {
                    throw new Exception('Inherit cancelled by event');
                }

                $insertData = array(
                    'tree_id'       => $treeId,
                    'eid'           => $eid,
                    'layoutarea_id' => $layoutAreaId,
                    'teaser_eid'    => $teaserEid,
                    'type'          => 'stop',
                    'modify_uid'    => MWF_Env::getUid(),
                    'modify_time'   => $db->fn->now(),
                    'stop_inherit'  => true,
                );

                $db->insert($db->prefix . 'element_tree_teasers', $insertData);
                $stopInheritId = $db->lastInsertId($db->prefix . 'element_tree_teasers');

                $msg = 'Inheritance stopped';

                $event = new Makeweb_Teasers_Event_StopInheritInheritedTeaser($treeId, $eid, $teaserEid, $layoutAreaId, $stopInheritId);
                $dispatcher->dispatch($event);
            }

            $result = MWF_Ext_Result::encode(true, null, $msg);
        } catch (Exception $e) {
            $result = MWF_Ext_Result::encode(false, null, $e->getMessage());
        }

        $this->getResponse()->setAjaxPayload($result);
    }

    /**
     * @Route("/show", name="teasers_layout_show")
     */
    public function showAction()
    {
        $teaserId = (int) $this->_getParam('teaser_id');

        try {
            if (!$teaserId) {
                throw new Exception('Missing parameter.');
            }

            $container = $this->getContainer();
            $db = $container->dbPool->write;
            $dispatcher = $container->get('event_dispatcher');

            $node = new Makeweb_Teasers_Node($teaserId);

            $beforeEvent = new Makeweb_Teasers_Event_BeforeShowTeaser($node);
            if (false === $dispatcher->dispatch($beforeEvent)) {
                throw new Exception('Show cancelled by event');
            }

            $db->update(
                $db->prefix . 'element_tree_teasers',
                array('no_display' => 0),
                array('id = ?' => $teaserId)
            );

            $event = new Makeweb_Teasers_Event_ShowTeaser($node);
            $dispatcher->dispatch($event);

            $result = MWF_Ext_Result::encode(true, null, 'Teaser will be displayed.');
        } catch (Exception $e) {
            $result = MWF_Ext_Result::encode(false, null, $e->getMessage());
        }

        $this->getResponse()->setAjaxPayload($result);
    }

    /**
     * @Route("/hide", name="teasers_layout_hide")
     */
    public function hideAction()
    {
        $teaserId = (int) $this->_getParam('teaser_id');

        try {
            if (!$teaserId) {
                throw new Exception('Missing parameter.');
            }

            $container = $this->getContainer();
            $db = $container->dbPool->write;
            $dispatcher = $container->get('event_dispatcher');

            $node = new Makeweb_Teasers_Node($teaserId);

            $beforeEvent = new Makeweb_Teasers_Event_BeforeHideTeaser($node);
            if (false === $dispatcher->dispatch($beforeEvent)) {
                throw new Exception('Hide cancelled by event');
            }

            $db->update(
                $db->prefix . 'element_tree_teasers',
                array('no_display' => 1),
                array('id = ?' => $teaserId)
            );

            $event = new Makeweb_Teasers_Event_HideTeaser($node);
            $dispatcher->dispatch($event);

            $msg = 'Teaser will not be displayed.';

            $result = MWF_Ext_Result::encode(true, null, $msg);
        } catch (Exception $e) {
            $result = MWF_Ext_Result::encode(false, null, $e->getMessage());
        }

        $this->getResponse()->setAjaxPayload($result);
    }

    /**
     * @Route("/showinherited", name="teasers_layout_showinherited")
     */
    public function showinheritedAction()
    {
        $layoutAreaId = (int) $this->_getParam('layoutarea_id');
        $treeId = (int) $this->_getParam('tree_id');
        $eid = (int) $this->_getParam('eid');
        $teaserEid = (int) $this->_getParam('teaser_eid');

        try {
            if (!$layoutAreaId || !$treeId || !$teaserEid) {
                throw new Exception('Missing parameter.');
            }

            $container = $this->getContainer();
            $db = $container->dbPool->write;
            $dispatcher = $container->get('event_dispatcher');

            $select = $db->select()
                ->from($db->prefix . 'element_tree_teasers', array('id'))
                ->where('tree_id = ?', $treeId)
                ->where('layoutarea_id = ?', $layoutAreaId)
                ->where('teaser_eid = ?', $teaserEid)
                ->where($db->quoteIdentifier('type') . ' = ?', 'hide');

            $hideId = $db->fetchOne($select);

            if ($hideId) {
                $beforeEvent = new Makeweb_Teasers_Event_BeforeShowInheritedTeaser($treeId, $eid, $teaserEid, $layoutAreaId);
                if (false === $dispatcher->dispatch($beforeEvent)) {
                    throw new Exception('Show cancelled by event');
                }

                // delete hiding for this page
                $db->delete($db->prefix . 'element_tree_teasers', array('id = ?' => $hideId));

                $event = new Makeweb_Teasers_Event_ShowInheritedTeaser($treeId, $eid, $teaserEid, $layoutAreaId, $hideId);
                $dispatcher->dispatch($event);
            }

            $result = MWF_Ext_Result::encode(true, null, 'Teaser will be displayed.');
        } catch (Exception $e) {
            $result = MWF_Ext_Result::encode(false, null, $e->getMessage());
        }

        $this->getResponse()->setAjaxPayload($result);
    }

    /**
     * @Route("/hideinherited", name="teasers_layout_hideinherited")
     */
    public function hideinheritedAction()
    {
        $layoutAreaId = (int) $this->_getParam('layoutarea_id');
        $treeId = (int) $this->_getParam('tree_id');
        $eid = (int) $this->_getParam('eid');
        $teaserEid = (int) $this->_getParam('teaser_eid');

        try {
            if (!$layoutAreaId || !$treeId || !$eid || !$teaserEid) {
                throw new Exception('Missing parameter.');
            }

            $container = $this->getContainer();
            $db = $container->dbPool->write;
            $dispatcher = $container->get('event_dispatcher');

            // type may be teaser or hide
            $select = $db->select()
                ->from($db->prefix . 'element_tree_teasers', 'id')
                ->where('tree_id = ?', $treeId)
                ->where('teaser_eid = ?', $teaserEid)
                ->where($db->quoteIdentifier('type') . ' = ?', 'hide');

            $hideId = (int) $db->fetchOne($select);

            if (!$hideId) {
                $beforeEvent = new Makeweb_Teasers_Event_BeforeHideInheritedTeaser($treeId, $eid, $teaserEid, $layoutAreaId);
                if (false === $dispatcher->dispatch($beforeEvent)) {
                    throw new Exception('Hide cancelled by event');
                }

                // Teaser is inherited and should hidden
                $insertData = array(
                    'tree_id'       => $treeId,
                    'eid'           => $eid,
                    'layoutarea_id' => $layoutAreaId,
                    'teaser_eid'    => $teaserEid,
                    'modify_uid'    => MWF_Env::getUid(),
                    'modify_time'   => $db->fn->now(),
                    'type'          => 'hide',
                    'no_display'    => 1,
                );

                $db->insert($db->prefix . 'element_tree_teasers', $insertData);
                $hideId = $db->lastInsertId($db->prefix . 'element_tree_teasers');

                $event = new Makeweb_Teasers_Event_HideInheritedTeaser($treeId, $eid, $teaserEid, $layoutAreaId, $hideId);
                $dispatcher->dispatch($event);
            }

            $msg = 'Teaser will not be displayed.';

            $result = MWF_Ext_Result::encode(true, null, $msg);
        } catch (Exception $e) {
            $result = MWF_Ext_Result::encode(false, null, $e->getMessage());
        }

        $this->getResponse()->setAjaxPayload($result);
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
                throw new Exception('Teaser sort cancelled by event');
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
