<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Controller;

use Phlexible\Bundle\AccessControlBundle\ContentObject\ContentObjectInterface;
use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\SecurityBundle\Acl\Acl;
use Phlexible\Bundle\TreeBundle\Tree\Node\TreeNodeInterface;
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
        $treeManager = $this->get('phlexible_tree.manager');
        $elementService = $this->get('phlexible_element.service');

        // TODO: switch to master language of element
        $defaultLanguage = $this->container->getParameter('phlexible_cms.languages.default');

        $siterootId = $request->get('siteroot_id');
        $tid = $request->get('node');
        $language = $request->get('language');

        $tree = $treeManager->getBySiteRootId($siterootId);
        $rootNode = $tree->getRoot();

        $data = array();
        if ($rootNode) {
            if ($tid === null || $tid < 1) {
                $data = array($this->getNodeData($rootNode, $language));
            } else {
                $node = $tree->get($tid);

                // check if children of this node should be shown
                $element = $elementService->findElement($node->getTypeId());
                $elementtype = $elementService->findElementtype($element);

                $nodes = $tree->getChildren($node);
                if (!empty($nodes) && !$elementtype->getHideChildren()) {
                    $data = $this->recurseNodes($nodes, $language);
                }
            }
        }

        return new JsonResponse($data);
    }

    /**
     * Return the Element data tree
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/teaserreference", name="tree_teaserreference")
     */
    public function teaserreferenceAction(Request $request)
    {
        // TODO: switch to master language of element
        $defaultLanguage = $this->container->getParameter('phlexible_cms.languages.default');

        $siterootId = $request->get('siteroot_id');
        $tid = $request->get('node');
        $language = $request->get('language');

        $treeManager = $this->get('phlexible_tree.manager');
        $elementService = $this->get('phlexible_element.service');

        $tree = $treeManager->getBySiteRootId($siterootId);

        $rootNode = $tree->getRoot();

        $data = array();
        if ($rootNode !== null) {
            if ($tid === null || $tid < 1) {
                $data = array($this->getNodeData($rootNode, $language));

                return new JsonResponse($data);
            }

            $node = $tree->getNode($tid);
            $nodes = $tree->getChildren($tid);

            $data = $this->recurseNodes($nodes, $language);

            foreach ($data as $key => $row) {
                if ($row['leaf']) {
                    unset($data[$key]);
                    continue;
                }
            }

            $data[$key]['cls'] = (!empty($data[$key]['cls']) ? $data[$key]['cls'] . ' ' : '') . 'node-disabled';
        }

        $currentTreeId = $tid;

        $teaserManager = Makeweb_Teasers_Manager::getInstance();
        $layoutAreaManager = Makeweb_Teasers_Layoutarea_Manager::getInstance();

        $element = $elementService->getByEID($node->getEid());

        $layoutAreas = $layoutAreaManager->getFor($element->getElementTypeID());

        foreach ($layoutAreas as $layoutArea) {
            $areaRoot = array(
                'id'         => 'area_' . $layoutArea->getID(),
                'area_id'    => $layoutArea->getID(),
                'parent_tid' => $currentTreeId,
                'parent_eid' => $element->getEid(),
                'icon'       => $layoutArea->getIconUrl(),
                'text'       => $layoutArea->getTitle(),
                'type'       => 'area',
                'inherited'  => null, //true,
                'leaf'       => false,
                'expanded'   => true,
                'allowDrag'  => false,
                'allowDrop'  => false,
                'children'   => array(),
                'qtip'       => $this->getContainer()->get('translator')->trans(
                        'elements.doubleclick_to_sort',
                        array(),
                        'gui'
                    ),
            );

            $teasers = $teaserManager->getAllByTID(
                $currentTreeId,
                $layoutArea->getID(),
                $language,
                false,
                array(),
                true
            );

            foreach ($teasers as $teaserArray) {
                switch ($teaserArray['type']) {
                    case 'teaser':
                        $teaser = $teaserManager->getByEID($teaserArray['teaser_eid']);
                        $teaserNode = new Makeweb_Teasers_Node($teaserArray['id']);

                        $areaRoot['children'][] = array(
                            'id'            => $teaserArray['id'],
                            'parent_tid'    => $currentTreeId,
                            'parent_eid'    => $element->getEid(),
                            'layoutarea_id' => $layoutArea->getID(),
                            'icon'          => $teaser->getIconUrl($teaserArray->getIconParams($language)),
                            'text'          => $teaser->getBackendTitle($language, $element->getMasterLanguage()),
                            // . ' [' . $teaser->getEid() . ']',
                            'eid'           => $teaser->getEid(),
                            'type'          => 'teaser',
                            'expanded'      => false,
                            'leaf'          => true,
                            'allowDrag'     => false,
                            'allowDrop'     => false,
                            'children'      => array()
                        );

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

        $elementService = $this->get('phlexible_element.service');

        $element = $elementService->findElement($eid);
        $elementVersion = $elementService->findLatestElementVersion($element);
        $elementtype = $elementService->findElementtype($element);

        $childrenIds = $elementService->getElementtypeService()->findAllowedChildrenIds($elementtype);

        $data = array();
        foreach ($childrenIds as $childId) {
            $elementtypeVersion = $elementService->getElementtypeService()->findLatestElementtypeVersion($elementtype);

            $elementtypeType = $elementtypeVersion->getElementType()->getType();

            if ($elementtypeType != Elementtype::TYPE_FULL &&
                $elementtypeType != Elementtype::TYPE_STRUCTURE
            ) {
                continue;
            }

            $data[$elementtype->getTitle()] = array(
                'id'    => $childId,
                'title' => $elementtype->getTitle(),
                'icon'  => '/bundles/phlexibleelementtype/elementtypes/' . $elementtype->getIcon(),
                'type'  => $elementtypeType,
            );
        }
        ksort($data);
        $data = array_values($data);

        return new JsonResponse(array('elementtypes' => $data));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/childelements", name="tree_childelements")
     */
    public function childelementsAction(Request $request)
    {
        $treeManager = $this->get('phlexible_tree.manager');
        $elementService = $this->get('phlexible_element.service');

        $id = $request->get('id');
        $tree = $treeManager->getByNodeID($id);
        $node = $tree->get($id);
        $eid = $node->getTypeId();
        $element = $elementService->findElement($eid);

        $language = $request->get('language', $element->getMasterLanguage());

        $firstString = $this->get('translator')->trans('elements.first', array(), 'gui');

        $data = array();
        $data[] = array(
            'id'    => '0',
            'title' => $firstString,
            'icon'  => '/bundles/phlexibleelementtype/elementtypes/_top.gif'
        );

        foreach ($tree->getChildren($node) as $childNode) {
            $childElement = $elementService->findElement($childNode->getTypeId());
            $childElementVersion = $elementService->findLatestElementVersion($childElement);
            $childElementtype = $elementService->findElementtype($childElement);

            $data[] = array(
                'id'    => $childNode->getId(),
                'title' => $childElementVersion->getBackendTitle($language, $childElementVersion->getElement()->getMasterLanguage()),
                'icon'  => '/bundles/phlexibleelementtype/elementtypes/' . $childElementtype->getIcon()
            );
        }

        return new JsonResponse(array('elements' => $data));
    }

    /**
     * Create the element data tree
     *
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return array
     */
    private function getNodeData(TreeNodeInterface $node, $language)
    {
        $securityContext = $this->get('security.context');
        $elementService = $this->get('phlexible_element.service');
        $router = $this->get('router');

        $userRights = array();
        if ($node instanceof ContentObjectInterface) {
            if (!$securityContext->isGranted(Acl::RESOURCE_SUPERADMIN)) {
                if ($securityContext->isGranted(array('right' => 'VIEW', 'language' => $language), $node)) {
                    return null;
                }

                $userRights = array(); //$contentRightsManager->getRights($language);
                $userRights = array_keys($userRights);
            } else {
                $userRights = array_keys(
                    $this->get('phlexible_access_control.permissions')->getByContentClass(get_class($node))
                );
            }
        }

        //$lockManager = MWF_Core_Locks_Manager::getInstance();

        $eid = $node->getTypeId();
        $element = $elementService->findElement($eid);
        $elementVersion = $elementService->findLatestElementVersion($element);

        //$identifier = new Makeweb_Elements_Element_Identifier($eid);
        $lockQtip = '';
        /*
        #if ($lockInfo = $lockManager->getLockInformation($identifier))
        #{
        #    if ($lockInfo['lock_uid'] == MWF_Env::getUid())
        #    {
        #        $lockQtip = '<hr>Locked by me.';
        #    }
        #    else
        #    {
        #        try
        #        {
        #            $user = MWF_Core_Users_User_Peer::getByUserID($lockInfo['lock_uid']);
        #        }
        #        catch (Exception $e)
        #        {
        #            $user = MWF_Core_Users_User_Peer::getSystemUser();
        #        }
        #
        #        $lockQtip = '<hr>Locked by '.$user->getUsername().'.';
        #    }
        #}
        */

        $elementtypeVersion = $elementService->findElementtypeVersion($elementVersion);
        $elementtype = $elementtypeVersion->getElementtype();

        $allowedElementTypeIds = $elementService->getElementtypeService()->findAllowedChildrenIds($elementtype);

        $qtip = 'TID: ' . $node->getId() . '<br />' .
            'EID: ' . $element->getEid() . '<br />' .
            'Version: ' . $elementVersion->getVersion() . '<br />' .
            '<hr>' .
            'Element Type: ' . $elementtype->getTitle() . '<br />' .
            'Element Type Version: ' . $elementtypeVersion->getVersion() . ' [' . $elementtypeVersion->getVersion(
            ) . ']' .
            $lockQtip;

        $data = array(
            'id'                  => $node->getID(),
            'eid'                 => $element->getEid(),
            'text'                => $elementVersion->getBackendTitle($language, $element->getMasterLanguage()),
            'icon'                => $router->generate('elements_asset', array('icon' => $elementtype->getIcon())),
            'navigation'          => 1, //$node->inNavigation($elementVersion->getVersion()),
            'restricted'          => 0, //$node->isRestricted($elementVersion->getVersion()),
            'element_type'        => $elementtype->getTitle(),
            'element_type_id'     => $elementtype->getId(),
            'element_type_type'   => $elementtype->getType(),
            'alias'               => 1, //$node->isInstance(),
            'allow_drag'          => true,
            'sort_mode'           => $node->getSortMode(),
            'areas'               => array(355),
            'allowed_et'          => $allowedElementTypeIds,
            'is_published'        => 1, //$node->isPublished($language),
            'rights'              => $userRights,
            'qtip'                => $qtip,
            'allow_children'      => $elementtype->getHideChildren() ? false : true,
            'default_tab'         => $elementtype->getDefaultTab(),
            'default_content_tab' => $elementtypeVersion->getDefaultContentTab(),
            'masterlanguage'      => $element->getMasterLanguage()
        );

        if (count($node->getTree()->getChildren($node)) && !$elementtype->getHideChildren()) {
            $data['leaf'] = false;
            $data['expanded'] = false;
        } else {
            $data['leaf'] = true;
            $data['expanded'] = false;
        }

        if ($node->isRoot()) {
            $data['cls'] = 'siteroot-node';
            $data['expanded'] = true;
        }

        return $data;
    }

    /**
     * Create the subnodes of an element data tree
     *
     * @param array  $nodes
     * @param string $language
     *
     * @return array
     */
    private function recurseNodes(array $nodes, $language)
    {
        $return = array();

        foreach ($nodes as $node) {
            $nodeData = $this->getNodeData($node, $language);

            if ($nodeData) {
                $return[] = $nodeData;
            }
        }

        return $return;
    }

    /**
     * @param string $siteRootId
     * @param string $language
     * @param array  $elementtypeIds
     *
     * @return array
     */
    private function findLinkNodes($siteRootId, $language, array $elementtypeIds)
    {
        $db = $container->dbPool->read;
        $treeManager = $container->elementsTreeManager;
        $elementManager = $container->elementsManager;

        $select = $db->select()
            ->distinct()
            ->from(array('et' => $db->prefix . 'element_tree'), array('id'))
            ->join(array('e' => $db->prefix . 'element'), 'et.eid = e.eid', array())
            ->where('et.siteroot_id = ?', $siteRootId)
            ->where('e.element_type_id IN (?)', $elementtypeIds)
            ->order('et.sort');

        $tids = $db->fetchCol($select);

        $data = array();

        $rootTid = null;

        foreach ($tids as $tid) {
            $node = $treeManager->getNodeByNodeId($tid);

            $element = $elementManager->getByEID($node->getEid());
            $elementVersion = $element->getVersion();

            if (!isset($data[$tid])) {
                $data[$node->getId()] = array(
                    'id'       => $node->getId(),
                    'eid'      => $node->getEid(),
                    'text'     => $elementVersion->getBackendTitle(
                            $language,
                            $element->getMasterLanguage()
                        ) . ' [' . $tid . ']',
                    'icon'     => $elementVersion->getIconUrl(),
                    'children' => array(),
                    'leaf'     => true,
                    'expanded' => false,
                    'disabled' => !in_array($elementVersion->getElementTypeID(), $elementtypeIds),
                );
            }

            do {
                $parentNode = $node->getParentNode();

                if (!$parentNode) {
                    $rootTid = $node->getId();
                    break;
                }

                if (!isset($data[$parentNode->getId()])) {
                    $element = $elementManager->getByEID($parentNode->getEid());
                    $elementVersion = $element->getVersion();

                    $data[$parentNode->getId()] = array(
                        'id'       => $parentNode->getId(),
                        'eid'      => $parentNode->getEid(),
                        'text'     => $elementVersion->getBackendTitle(
                                $language,
                                $element->getMasterLanguage()
                            ) . ' [' . $parentNode->getId() . ']',
                        'icon'     => $elementVersion->getIconUrl(),
                        'children' => array(),
                        'leaf'     => false,
                        'expanded' => false,
                        'disabled' => !in_array($elementVersion->getElementTypeID(), $elementtypeIds),
                    );
                } else {
                    $data[$parentNode->getId()]['leaf'] = false;
                }

                $data[$parentNode->getId()]['children'][$node->getId()] =& $data[$node->getId()];

                $node = $parentNode;
            } while ($parentNode);
        }

        if (!count($data)) {
            return array();
        }

        $data = $this->_stripLinkNodeKeys($data[$rootTid], $db);

        return $data['children'];
    }

    protected function _stripLinkNodeKeys($data, Zend_Db_Adapter_Abstract $db)
    {
        if (is_array($data['children']) && count($data['children'])) {
            $sortSelect = $db->select()
                ->from($db->prefix . 'element_tree', array('id', 'sort'))
                ->where('parent_id = ?', $data['id'])
                ->where('id IN (?)', array_keys($data['children']))
                ->order('sort');

            $sortTids = $db->fetchPairs($sortSelect);
            $sortedTids = array();
            foreach (array_keys($data['children']) as $tid) {
                $sortedTids[$tid] = $sortTids[$tid];
            }

            array_multisort($sortedTids, $data['children']);

            $data['children'] = array_values($data['children']);

            foreach ($data['children'] as $key => $item) {
                $data['children'][$key] = $this->_stripLinkNodeKeys($item, $db);
            }
        }

        return $data;
    }

    /**
     * Recurse over tree nodes
     *
     * @param array             $nodes
     * @param string            $language
     * @param int               $mode
     * @param TreeNodeInterface $targetNode
     *
     * @return array
     */
    private function recurseLinkNodes(array $nodes, $language, $mode, TreeNodeInterface $targetNode = null)
    {
        $elementService = $this->get('phlexible_element.service');

        $data = array();

        foreach ($nodes as $node) {
            /* @var $node TreeNodeInterface */

            $element = $elementService->findElement($node->getTypeId());
            $elementVersion = $elementService->findLatestElementVersion($element);
            $elementtype = $elementService->findElementtype($element);

            $tid = $node->getId();
            $tree = $node->getTree();
            $children = $tree->getChildren($node);

            $dataNode = array(
                'id'       => $node->getId(),
                'eid'      => $node->getTypeId(),
                'text'     => $elementVersion->getBackendTitle(
                        $language,
                        $element->getMasterLanguage()
                    ) . ' [' . $tid . ']',
                'icon'     => '/bundles/phlexibleelementtype/elementtypes/' . $elementtype->getIcon(),
                'children' => !$tree->hasChildren($node)
                        ? array()
                        : $mode == self::MODE_NOET_TARGET && $tree->isParentOf($node, $targetNode)
                            ? $this->recurseLinkNodes($children, $language, $mode, $targetNode)
                            : false,
                'leaf'     => !$tree->hasChildren($node),
                'expanded' => false,
            );

            /*
            $leafCount = 0;
            if (is_array($dataNode['children']))
            {
                foreach($dataNode['children'] as $child)
                {
                    $leafCount += $child['leafCount'];
                    if (!isset($child['disabled']) || !$child['disabled'])
                    {
                        ++$leafCount;
                    }
                }
            }
            $dataNode['leafCount'] = $leafCount;
            */

            $data[] = $dataNode;
        }

        return $data;
    }

    /**
     * Return the Element data tree
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/link", name="tree_link")
     */
    public function linkAction(Request $request)
    {
        $currentSiterootId = $request->get('siteroot_id');
        $id = $request->get('node', 'root');
        $language = $request->get('language');
        $recursive = (bool) $request->get('recursive');

        $treeManager = $this->get('phlexible_tree.manager');
        $elementService = $this->get('phlexible_element.service');

        if (null === $language) {
            if ($id != 'root') {
                $tree = $treeManager->getByNodeID($id);
                $node = $tree->get($id);
            } else {
                $tree = $treeManager->getBySiteRootId($currentSiterootId);
                $node = $tree->getRoot();
            }
            $element = $elementService->findElement($node->getTypeId());
            $language = $element->getMasterLanguage();
        }

        if ($id === 'root') {
            $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');
            $siteroots = $siterootManager->findAll();

            // move current siteroot to the beginning
            if ($currentSiterootId !== null) {
                foreach ($siteroots as $index => $siteroot) {
                    if ($siteroot->getId() === $currentSiterootId) {
                        array_unshift($siteroots, $siteroots[$index]);
                        unset($siteroots[$index]);
                    }
                }
            }

            $data = array();
            foreach ($siteroots as $siteroot) {
                $siterootId = $siteroot->getId();
                $tree = $treeManager->getBySiteRootID($siterootId);
                $rootNode = $tree->getRoot();

                $element = $elementService->findElement($rootNode->getTypeId());
                $elementVersion = $elementService->findLatestElementVersion($element);
                $elementtype = $elementService->findElementtype($element);

                $data[] = array(
                    'id'       => $rootNode->getId(),
                    'eid'      => (int) $rootNode->getTypeId(),
                    'text'     => $siteroot->getTitle(),
                    'icon'     => '/bundles/phlexibleelementtype/elementtypes/' . $elementtype->getIcon(),
                    // 'cls'      => 'siteroot-node',
                    // 'children' => $startNode->hasChildren() ? $this->_recurseNodes($startNode->getChildren(), $language) : array(),
                    'leaf'     => !$tree->hasChildren($rootNode),
                    'expanded' => $siterootId === $currentSiterootId,
                );
            }
        } else {
            $tree = $treeManager->getByNodeID($id);
            $node = $tree->get($id);
            $nodes = $tree->getChildren($node);
            $data = $this->recurseLinkNodes($nodes, $language, $recursive);
        }

        return new JsonResponse($data);
    }

    /**
     * Return the Element data tree
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/linkelement", name="tree_link_element")
     */
    public function linkelementAction(Request $request)
    {
        $siterootId = $request->get('siteroot_id');
        $id = $request->get('node', 'root');
        $language = $request->get('language');
        $targetTid = $request->get('value');
        $elementtypeIds = $request->get('element_type_ids');

        if ($elementtypeIds) {
            $elementtypeIds = explode(',', $elementtypeIds);
        } else {
            $elementtypeIds = array();
        }

        $treeManager = $this->get('phlexible_tree.manager');
        $elementService = $this->get('phlexible_element.service');

        if (!$language) {
            if ($id != 'root') {
                $tree = $treeManager->getByNodeId($id);
                $node = $tree->get($id);
            } else {
                $tree = $treeManager->getBySiteRootId($siterootId);
                $node = $tree->getRoot();
            }

            $element = $elementService->findElement($node->getTypeId());
            $language = $element->getMasterLanguage();
        }

        $tree = $treeManager->getBySiteRootID($siterootId);
        if ($id === 'root') {
            $startNode = $tree->getRoot();
        } else {
            $startNode = $tree->get($id);
        }

        $targetNode = null;
        if ($targetTid) {
            $targetTree = $treeManager->getByNodeId($targetTid);
            $targetNode = $targetTree->get($targetTid);
        }

        if (!count($elementtypeIds)) {
            $mode = !$targetTid ? self::MODE_NOET_NOTARGET : self::MODE_NOET_TARGET;

            $nodes = $tree->getChildren($startNode);
            $data = $this->recurseLinkNodes($nodes, $language, $mode, $targetNode);
        } else {
            $mode = !$targetTid ? self::MODE_ET_NOTARGET : self::MODE_ET_TARGET;

            $data = $this->findLinkNodes($startNode->getTree()->getSiterootId(), $language, $elementtypeIds);

            if ($elementtypeIds) {
                $data = $this->recursiveTreeStrip($data);
            }
        }

        return new JsonResponse($data);
    }

    /**
     * Strip all disabled nodes recursivly
     *
     * @param  array $data
     *
     * @return array
     */
    private function recursiveTreeStrip(array $data)
    {
        if (count($data) === 1 && !empty($data[0]['children'])) {
            return $this->recursiveTreeStrip($data[0]['children']);
        }

        return $data;
    }

    /**
     * @Route("/linkintrasiteroot", name="tree_linkintrasiteroot")
     */
    public function linkintrasiterootAction()
    {
        $container = $this->getContainer();

        $treeManager = $container->get('phlexible_tree.manager');
        $elementVersionManager = $container->elementsVersionManager;

        // TODO: switch to master language of element
        $defaultLanguage = $container->getParam(':phlexible_cms.languages.default');

        $siterootID = $this->_getParam('siteroot_id', null);
        $id = $this->_getParam('node', 'root');
        $recursive = (bool) $this->_getParam('recursive', false);
        $language = $this->_getParam('language', null);
        $elementtypeIds = $this->_getParam('element_type_ids', array());
        $targetTid = $this->_getParam('value', null);

        if (!$language) {
            $language = $defaultLanguage;
        }

        if ($elementtypeIds) {
            $elementtypeIds = explode(',', $elementtypeIds);
        } else {
            $elementtypeIds = array();
        }

        $targetNode = null;
        if ($targetTid) {
            $targetNode = $treeManager->getNodeByNodeID($targetTid);
        }

        if ($id == 'root') {
            $siteRootManager = Makeweb_Siteroots_Siteroot_Manager::getInstance();
            $siteRoots = $siteRootManager->getAllSiteRoots();

            if ($siterootID !== null) {
                unset($siteRoots[$siterootID]);
            }

            $data = array();
            foreach ($siteRoots as $siteRootID => $siteRoot) {
                $tree = $treeManager->getBySiteRootID($siteRootID);
                $rootNode = $tree->getRoot();

                $elementVersion = $elementVersionManager->getLatest($rootNode->getEID());

                $children = false;
                if ($targetNode && $siteRootID === $targetNode->getSiteRootId()) {
                    if (!count($elementtypeIds)) {
                        $mode = !$targetTid ? self::MODE_NOET_NOTARGET : self::MODE_NOET_TARGET;

                        $nodes = $rootNode->getChildren();
                        $children = $this->recurseLinkNodes($nodes, $language, $mode, $targetNode);
                    } else {
                        $mode = !$targetTid ? self::MODE_ET_NOTARGET : self::MODE_ET_TARGET;

                        $children = $this->findLinkNodes($rootNode->getSiteRootId(), $language, $elementtypeIds);

                        if ($elementtypeIds) {
                            $children = $this->recursiveTreeStrip($children);
                        }
                    }
                }

                $data[] = array(
                    'id'       => $rootNode->getID(),
                    'eid'      => $rootNode->getEID(),
                    'text'     => $siteRoot->getTitle(),
                    'icon'     => $elementVersion->getIconUrl(),
                    'children' => $children,
                    'leaf'     => !$rootNode->hasChildren(),
                    'expanded' => false
                );
            }
        } else {
            $startNode = $treeManager->getNodeByNodeID($id);

            if (!count($elementtypeIds)) {
                $mode = !$targetTid ? self::MODE_NOET_NOTARGET : self::MODE_NOET_TARGET;

                $nodes = $startNode->getChildren();
                $data = $this->recurseLinkNodes($nodes, $language, $mode, $targetNode);
            } else {
                $mode = !$targetTid ? self::MODE_ET_NOTARGET : self::MODE_ET_TARGET;

                $data = $this->findLinkNodes($startNode->getSiteRootId(), $language, $elementtypeIds);

                if ($elementtypeIds) {
                    $data = $this->recursiveTreeStrip($data);
                }
            }

            //$nodes = $startNode->getChildren();
            //$data = $this->_recurseLinkNodes($nodes, $language, $mode);
        }

        $this->getResponse()->setAjaxPayload($data);
    }

    /**
     * Create an Element
     *
     * @Route("/create", name="tree_create")
     */
    public function createAction()
    {
        $container = $this->getContainer();

        try {
            $db = $container->dbPool->default;
            $db->beginTransaction();

            $filters = array(
                '*' => array('StringTrim', 'StripTags'),
            );

            $validators = array(
                'id'              => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'siteroot_id'     => array(
                    'Uuid',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'element_type_id' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'prev_id'         => array(
                    'Int',
                    Zend_Filter_Input::DEFAULT_VALUE => 0,
                ),
                'sort'            => array(
                    'Alpha',
                    Zend_Filter_Input::ALLOW_EMPTY   => true,
                    Zend_Filter_Input::DEFAULT_VALUE => 'free',
                ),
                'navigation'      => array(
                    'Alpha',
                    Zend_Filter_Input::ALLOW_EMPTY   => true,
                    Zend_Filter_Input::DEFAULT_VALUE => '',
                ),
                'restricted'      => array(
                    'Alpha',
                    Zend_Filter_Input::ALLOW_EMPTY   => true,
                    Zend_Filter_Input::DEFAULT_VALUE => '',
                ),
                'masterlanguage'  => array(
                    'Alpha',
                    Zend_Filter_Input::PRESENCE      => Zend_Filter_Input::PRESENCE_REQUIRED,
                    Zend_Filter_Input::DEFAULT_VALUE => $container->getParam(':frontend.languages.frontend')
                )
            );

            $fi = new Brainbits_Filter_Input($filters, $validators, $this->_getAllParams());

            if (!$fi->isValid()) {
                throw new Brainbits_Filter_Exception('Error occured', 0, $fi);
            }

            $parentId = $fi->id;
            $siterootId = $fi->siteroot_id;
            $elementtypeId = $fi->element_type_id;
            $prevId = $fi->prev_id;
            $sortMode = $fi->sort;
            $navigation = $fi->navigation ? true : false;
            $restricted = $fi->restricted ? true : false;
            $masterLanguage = $fi->masterlanguage;

            $elementManager = Makeweb_Elements_Element_Manager::getInstance();
            $treeManager = Makeweb_Elements_Tree_Manager::getInstance();
            $tree = $treeManager->getBySiteRootId($siterootId);

            $newElement = $elementManager->create($elementtypeId, true, $masterLanguage);
            $newEid = $newElement->getEid();
            $newElementVersion = $newElement->getLatestVersion();

            // $siterootId, $parentId, $prevId, $navigation, $restricted

            // place new element in element_tree
            $newTreeId = $tree->add($parentId, $newEid, $prevId, 'element', $sortMode);

            if ($navigation !== null || $restricted !== null) {
                $tree->setPage(
                    $newTreeId,
                    $newElementVersion->getVersion(),
                    $navigation,
                    $restricted
                );
            }

            $db->commit();

            $result = MWF_Ext_Result::encode(
                true,
                $newEid,
                'Element EID "' . $newEid . ' (' . $masterLanguage . ')" created.',
                array(
                    'eid'             => $newEid,
                    'tid'             => $newTreeId,
                    'master_language' => $masterLanguage,
                    'navigation'      => $navigation,
                    'restricted'      => $restricted
                )
            );
        } catch (Exception $e) {
            $result = MWF_Ext_Result::exception($e);
        }

        $this->getResponse()->setAjaxPayload($result);
    }

    /**
     * Create an Element
     *
     * @Route("/createinstance", name="tree_createinstance")
     */
    public function createinstanceAction()
    {
        try {
            $db = $this->getContainer()->dbPool->default;
            $db->beginTransaction();

            $filters = array(
                '*' => array('StringTrim', 'StripTags'),
            );

            $validators = array(
                'id'          => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'for_tree_id' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'prev_id'     => array(
                    'Int',
                    Zend_Filter_Input::DEFAULT_VALUE => 0,
                ),
            );

            $fi = new Brainbits_Filter_Input($filters, $validators, $this->_getAllParams());

            if (!$fi->isValid()) {
                throw new Brainbits_Filter_Exception('Error occured', 0, $fi);
            }

            $parentId = $fi->id;
            $instanceId = $fi->for_tree_id;
            $prevId = $fi->prev_id;

            $manager = Makeweb_Elements_Tree_Manager::getInstance();

            $targetNode = $manager->getNodeByNodeId($parentId);
            $tree = $targetNode->getTree();

            $tree->createAlias($parentId, $instanceId, $prevId);

            $db->commit();

            $result = MWF_Ext_Result::encode(true, 0, 'Alias created.');
        } catch (Exception $e) {
            $db->rollBack();
            $result = MWF_Ext_Result::exception($e);
        }

        $this->getResponse()->setAjaxPayload($result);
    }

    /**
     * Copy an Element
     *
     * @Route("/copy", name="tree_copy")
     */
    public function copyAction()
    {
        try {
            $db = $this->getContainer()->dbPool->default;
            $db->beginTransaction();

            $filters = array(
                '*' => array('StringTrim', 'StripTags'),
            );

            $validators = array(
                'id'          => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'for_tree_id' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'prev_id'     => array(
                    'Int',
                    Zend_Filter_Input::ALLOW_EMPTY   => true,
                    Zend_Filter_Input::DEFAULT_VALUE => 0,
                ),
            );

            $fi = new Brainbits_Filter_Input($filters, $validators, $this->_getAllParams());

            if (!$fi->isValid()) {
                throw new Brainbits_Filter_Exception('Error occured', 0, $fi);
            }

            $parentId = $fi->id;
            $sourceId = $fi->for_tree_id;
            $prevId = $fi->prev_id;

            $elementManager = Makeweb_Elements_Element_Manager::getInstance();
            $treeManager = Makeweb_Elements_Tree_Manager::getInstance();

            $sourceNode = $treeManager->getNodeByNodeId($sourceId);
            $tree = $sourceNode->getTree();
            $sourceEid = $sourceNode->getEid();

            $select = $db->select()
                ->from($db->prefix . 'element', array('element_type_id', 'masterlanguage'))
                ->where('eid = ?', $sourceEid);

            $sourceElementRow = $db->fetchRow($select);

            $targetElement = $elementManager->create(
                $sourceElementRow['element_type_id'],
                false,
                $sourceElementRow['masterlanguage']
            );
            $targetEid = $targetElement->getEid();

            // place new element in element_tree
            $targetId = $tree->add($parentId, $targetEid, $prevId);

            // copy element version data
            $sourceElement = $elementManager->getByEid($sourceEid);
            $sourceElementVersion = $sourceElement->getLatestVersion();
            $targetElementVersion = $sourceElementVersion->copy($targetEid);

            // copy tree node settings
            $tree->copyPage(
                $sourceId,
                $targetElementVersion->getVersion(),
                $sourceElementVersion->getVersion(),
                $targetId
            );

            $db->commit();

            $result = MWF_Ext_Result::encode(true, $targetId, 'Element copied.');
        } catch (Exception $e) {
            $result = MWF_Ext_Result::exception($e);
        }

        $this->getResponse()->setAjaxPayload($result);
    }

    /**
     * Move an Element
     *
     * @Route("/move", name="tree_move")
     *
     */
    public function moveAction()
    {
        try {
            $filters = array('StringTrim', 'StripTags');

            $validators = array(
                'id'     => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'target' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                )
            );

            $fi = new Brainbits_Filter_Input($filters, $validators, $this->_getAllParams());

            if (!$fi->isValid()) {
                throw new Brainbits_Filter_Exception('Error occured', 0, $fi);
            }

            $id = $fi->id;
            $targetId = $fi->target;

            $treeManager = Makeweb_Elements_Tree_Manager::getInstance();
            $node = $treeManager->getNodeByNodeId($id);
            $tree = $node->getTree();

            if ($id === $targetId) {
                $result = MWF_Ext_Result::encode(false, null, 'source_id === target_id');
            } else {
                $tree->move($id, $targetId);

                $result = MWF_Ext_Result::encode(
                    true,
                    $id,
                    'Element moved.',
                    array('id' => $id, 'parent_id' => $targetId)
                );
            }
        } catch (Exception $e) {
            $result = MWF_Ext_Result::exception($e);
        }

        $this->getResponse()->setAjaxPayload($result);
    }

    /**
     * predelete action
     * check if element has instances
     *
     * @throws Brainbits_Filter_Exception
     *
     * @Route("/predelete", name="tree_predelete")
     */
    public function predeleteAction()
    {
        try {
            $filters = array('StringTrim', 'StripTags');

            $validators = array(
                'id' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
            );

            $fi = new Brainbits_Filter_Input($filters, $validators, $this->_getAllParams());

            if (!$fi->isValid()) {
                throw new Brainbits_Filter_Exception('Error occured', 0, $fi);
            }

            $treeId = $fi->id;

            $container = $this->getContainer();
            $treeManager = $container->get('phlexible_tree.manager');
            $db = $container->dbPool->read;

            $nodeId = $treeId[0];
            $node = $treeManager->getNodeByNodeId($nodeId);

            $sql = $db->select()
                ->from(
                    $db->prefix . 'element_tree',
                    array('id', 'siteroot_id', 'modify_time', 'instance_master')
                )
                ->where('eid = ?', $node->getEid());
            $instances = $db->fetchAll($sql);

            if (count($instances) > 1) {
                $siterootManager = $container->siterootManager;
                $treeHelper = $container->elementsTreeTreeHelper;

                $instancesArray = array();
                foreach ($instances as $instance) {
                    $siteroot = $siterootManager->getById($instance['siteroot_id']);
                    $instanceNode = $treeManager->getNodeByNodeId($instance['id']);
                    $instanceTitle = $treeHelper->getOnlineTitleByTid($instanceNode->getParentId(), 'de');

                    $instancesArray[] = array(
                        $instance['id'],
                        $siteroot->getTitle(),
                        $instanceTitle,
                        $instance['modify_time'],
                        (bool) $instance['instance_master'],
                        (bool) ($instance['id'] == $nodeId)
                    );
                }

                $result = MWF_Ext_Result::encode(true, $nodeId, '', $instancesArray);
            } else {
                $result = MWF_Ext_Result::encode(true, $nodeId, '', array());
            }

        } catch (Makeweb_Elements_Tree_Exception_LockException $e) {
            $result = MWF_Ext_Result::encode(false, $treeId, $e->getMessage());
        } catch (Exception $e) {
            $result = MWF_Ext_Result::exception($e);
        }

        $this->getResponse()->setAjaxPayload($result);
    }

    /**
     * Delete an Element
     *
     * @Route("/delete", name="tree_delete")
     */
    public function deleteAction()
    {
        try {
            $filters = array('StringTrim', 'StripTags');

            $validators = array(
                'id' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
            );

            $fi = new Brainbits_Filter_Input($filters, $validators, $this->_getAllParams());

            if (!$fi->isValid()) {
                throw new Brainbits_Filter_Exception('Error occured', 0, $fi);
            }

            $treeIds = $fi->id;
            if (!is_array($treeIds)) {
                $treeIds = array($treeIds);
            }

            $container = $this->getContainer();
            $db = $container->dbPool->write;
            $treeManager = $container->elementsTreeManager;

            $db->beginTransaction();

            foreach ($treeIds as $treeId) {
                $tree = $treeManager->getByNodeId($treeId);
                $tree->delete($treeId);
            }

            //$fileUsage = new Makeweb_Elements_Element_FileUsage(MWF_Registry::getContainer()->dbPool);
            //$fileUsage->update($eid);

            $db->commit();

            $result = MWF_Ext_Result::encode(true, $treeId, 'Item(s) deleted.');
        } catch (Makeweb_Elements_Tree_Exception_LockException $e) {
            $db->rollBack();
            $result = MWF_Ext_Result::encode(false, $treeId, $e->getMessage());
        } catch (Exception $e) {
            $db->rollBack();
            $result = MWF_Ext_Result::exception($e);
        }

        $this->getResponse()->setAjaxPayload($result);
    }
}
