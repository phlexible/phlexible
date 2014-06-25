<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Controller;

use Phlexible\Bundle\AccessControlBundle\ContentObject\ContentObjectInterface;
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
    const MODE_NOET_TARGET   = 2;
    const MODE_ET_NOTARGET   = 3;
    const MODE_ET_TARGET     = 4;

    /**
     * List all element types
     *
     * @Route("/childelementtypes", name="tree_childelementtypes")
     */
    public function childelementtypesAction()
    {
        $elementManager            = $container->elementsManager;
        $elementVersionManager     = $container->elementsVersionManager; //Makeweb_Elements_Element_Version_Manager::getInstance();
        $elementTypeVersionManager = Makeweb_Elementtypes_Elementtype_Version_Manager::getInstance();

        $eid      = $this->_getParam('eid');
        $element  = $elementManager->getByEID($eid);
        $language = $this->_getParam('language', $element->getMasterLanguage());

        $elementVersion = $elementVersionManager->getLatest($eid);

        $childrenIds = $elementVersion->getElementTypeVersionObj()->getAllowedChildrenIds();

        $data = array();
        foreach ($childrenIds as $childId)
        {
            /* @var $elementType Makeweb_Elementtypes_Elementtype */

            $elementTypeVersion = $elementTypeVersionManager->getLatest($childId);

            $elementTypeType = $elementTypeVersion->getElementType()->getType();

            if ($elementTypeType != Makeweb_Elementtypes_Elementtype_Version::TYPE_FULL &&
                $elementTypeType != Makeweb_Elementtypes_Elementtype_Version::TYPE_STRUCTURE)
            {
                continue;
            }

            $data[$elementTypeVersion->getTitle()] = array(
                'id'    => $childId,
                'title' => $elementTypeVersion->getTitle(),
                'icon'  => $elementTypeVersion->getIcon(),
                'type'  => $elementTypeType,
            );
        }
        ksort($data);
        $data = array_values($data);

        $this->getResponse()->setAjaxPayload(array('elementtypes' => $data));
    }

    /**
     * @Route("/childelements", name="tree_childelements")
     */
    public function childelementsAction()
    {
        $treeManager           = $this->get('phlexible_tree.manager');
        $elementManager        = $container->elementsManager;
        $elementVersionManager = $container->elementsVersionManager;

        $id       = $this->_getParam('id');
        $node     = $treeManager->getNodeByNodeID($id);
        $eid      = $node->getEid();
        $element  = $elementManager->getByEID($eid);
        $language = $this->_getParam('language', $element->getMasterLanguage());

        $firstString = $container->get('translator')->trans('elements.first', array(), 'gui');

        $data = array();
        $data[] = array(
            'id'    => '0',
            'title' => $firstString,
            'icon'  => $this->_request->getBaseUrl() . '/elements/asset/_top.gif'
        );

        foreach ($node->getChildren() as $childNode)
        {
            $elementVersion = $elementVersionManager->getLatest($childNode->getEid(), $language);

            $data[] = array(
                'id'    => $childNode->getId(),
                'title' => $elementVersion->getBackendTitle($language, $elementVersion->getElement()->getMasterLanguage()),
                'icon'  => $this->_request->getBaseUrl() . '/elements/asset/' . $elementVersion->getElementTypeVersionObj()->getIcon()
            );
        }

        $this->getResponse()->setAjaxPayload(array('elements' => $data));
    }

    /**
     * Create the element data tree
     *
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return array
     */
    protected function _getNodeData(TreeNodeInterface $node, $language)
    {
        $container = $this->getContainer();
        $securityContext = $container->get('security.context');
        $elementService = $container->get('phlexible_element.service');

        $userRights = array();
        if ($node instanceof ContentObjectInterface) {
            if (!$securityContext->isGranted(Acl::RESOURCE_SUPERADMIN)) {
                if ($securityContext->isGranted(array('right' => 'VIEW', 'language' => $language), $node)) {
                    return null;
                }

                $userRights = array();//$contentRightsManager->getRights($language);
                $userRights = array_keys($userRights);
            } else {
                $userRights = array_keys($this->getContainer()->get('accesscontrol.right.registry')->getRights('internal', 'treenode'));
            }
        }

        //$lockManager = MWF_Core_Locks_Manager::getInstance();

        $eid            = $node->getTypeId();
        $element        = $elementService->findElement($eid);
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

        $qtip = 'TID: '.$node->getId().'<br />'.
                'EID: '.$element->getEid().'<br />'.
                'Version: '.$elementVersion->getVersion().'<br />'.
                '<hr>'.
                'Element Type: '.$elementtype->getTitle().'<br />'.
                'Element Type Version: '.$elementtypeVersion->getVersion().' ['.$elementtypeVersion->getVersion().']'.
                $lockQtip;

        $data = array(
            'id'                  => $node->getID(),
            'eid'                 => $element->getEid(),
            'text'                => $elementVersion->getBackendTitle($language, $element->getMasterLanguage()),
            'icon'                => $this->get('router')->assemble(array('icon' => $elementtype->getIcon()), 'elements_asset'),
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
    protected function _recurseNodes(array $nodes, $language)
    {
        $return = array();

        foreach ($nodes as $node) {
            $nodeData = $this->_getNodeData($node, $language);

            if ($nodeData) {
                $return[] = $nodeData;
            }
        }

        return $return;
    }

    protected function _findLinkNodes($siteRootId, $language, array $elementTypeIds)
    {
        $container = $this->getContainer();
        $db             = $container->dbPool->read;
        $treeManager    = $container->elementsTreeManager;
        $elementManager = $container->elementsManager;

        $select = $db->select()
            ->distinct()
            ->from(array('et' => $db->prefix . 'element_tree'), array('id'))
            ->join(array('e' => $db->prefix . 'element'), 'et.eid = e.eid', array())
            ->where('et.siteroot_id = ?', $siteRootId)
            ->where('e.element_type_id IN (?)', $elementTypeIds)
            ->order('et.sort');

        $tids = $db->fetchCol($select);

        $data = array();

        $rootTid = null;

        foreach ($tids as $tid)
        {
            $node = $treeManager->getNodeByNodeId($tid);

            $element        = $elementManager->getByEID($node->getEid());
            $elementVersion = $element->getVersion();

            if (!isset($data[$tid]))
            {
                $data[$node->getId()] = array(
                    'id'       => $node->getId(),
                    'eid'      => $node->getEid(),
                    'text'     => $elementVersion->getBackendTitle($language, $element->getMasterLanguage()) . ' [' . $tid . ']',
                    'icon'     => $elementVersion->getIconUrl(),
                    'children' => array(),
                    'leaf'     => true,
                    'expanded' => false,
                    'disabled' => !in_array($elementVersion->getElementTypeID(), $elementTypeIds),
                );
            }

            do
            {
                $parentNode = $node->getParentNode();

                if (!$parentNode)
                {
                    $rootTid = $node->getId();
                    break;
                }

                if (!isset($data[$parentNode->getId()]))
                {
                    $element        = $elementManager->getByEID($parentNode->getEid());
                    $elementVersion = $element->getVersion();

                    $data[$parentNode->getId()] = array(
                        'id'       => $parentNode->getId(),
                        'eid'      => $parentNode->getEid(),
                        'text'     => $elementVersion->getBackendTitle($language, $element->getMasterLanguage()) . ' [' . $parentNode->getId() . ']',
                        'icon'     => $elementVersion->getIconUrl(),
                        'children' => array(),
                        'leaf'     => false,
                        'expanded' => false,
                        'disabled' => !in_array($elementVersion->getElementTypeID(), $elementTypeIds),
                    );
                }
                else
                {
                    $data[$parentNode->getId()]['leaf'] = false;
                }

                $data[$parentNode->getId()]['children'][$node->getId()] =& $data[$node->getId()];

                $node = $parentNode;
            }
            while ($parentNode);
        }

        if (!count($data))
        {
            return array();
        }

        $data = $this->_stripLinkNodeKeys($data[$rootTid], $db);

        return $data['children'];
    }

    protected function _stripLinkNodeKeys($data, Zend_Db_Adapter_Abstract $db)
    {
        if (is_array($data['children']) && count($data['children']))
        {
            $sortSelect = $db->select()
                ->from($db->prefix . 'element_tree', array('id', 'sort'))
                ->where('parent_id = ?', $data['id'])
                ->where('id IN (?)', array_keys($data['children']))
                ->order('sort');

            $sortTids = $db->fetchPairs($sortSelect);
            $sortedTids = array();
            foreach (array_keys($data['children']) as $tid)
            {
                $sortedTids[$tid] = $sortTids[$tid];
            }

            array_multisort($sortedTids, $data['children']);

            $data['children'] = array_values($data['children']);

            foreach ($data['children'] as $key => $item)
            {
                $data['children'][$key] = $this->_stripLinkNodeKeys($item, $db);
            }
        }

        return $data;
    }

    /**
     * Recurse over tree nodes
     *
     * @param array   $nodes
     * @param string  $language
     * @param integer $mode
     * @param array   $elementTypeIds
     * @param string  $targetTid
     * @return array
     */
    protected function _recurseLinkNodes(array $nodes, $language, $mode, Makeweb_Elements_Tree_Node $targetNode = null)
    {
        $elementManager = $this->getContainer()->elementsManager;

        $data = array();

        foreach ($nodes as $node)
        {
            /* @var $node Makeweb_Elements_Tree_Node */

            $element        = $elementManager->getByEID($node->getEid());
            $elementVersion = $element->getVersion();

            $tid      = $node->getId();
            $children = $node->getChildren();

            $dataNode = array(
                'id'       => $node->getId(),
                'eid'      => $node->getEid(),
                'text'     => $elementVersion->getBackendTitle($language, $element->getMasterLanguage()) . ' ['.$tid.']',
                'icon'     => $elementVersion->getIconUrl(),
                'children' => !$node->hasChildren()
                    ? array()
                    : $mode == self::MODE_NOET_TARGET && $node->isParentOf($targetNode)
                        ? $this->_recurseLinkNodes($children, $language, $mode, $targetNode)
                        : false,
                'leaf'     => !$node->hasChildren(),
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
     * @Route("/tree", name="tree_tree")
     */
    public function treeAction(Request $request)
    {
        $treeManager    = $this->get('phlexible_tree.manager');
        $elementService = $this->get('phlexible_element.service');

        // TODO: switch to master language of element
        $defaultLanguage = $this->container->getParameter('phlexible_cms.languages.default');

        $siterootId = $request->get('siteroot_id');
        $tid        = $request->get('node');
        $language   = $request->get('language');

        $tree     = $treeManager->getBySiteRootID($siterootId);
        $rootNode = $tree->getRoot();

        $data = array();
        if (null !== $rootNode) {
            if ($tid === null || $tid < 1) {
                $data = array($this->_getNodeData($rootNode, $language));
            } else {
                $node = $tree->get($tid);

                // check if children of this node should be shown
                $element        = $elementService->findElement($node->getTypeId());
                $elementtype = $elementService->findElementtype($element);

                $nodes = $tree->getChildren($node);
                if (!empty($nodes) && !$elementtype->getHideChildren()) {
                    $data  = $this->_recurseNodes($nodes, $language);
                }
            }
        }

        return new JsonResponse($data);
    }

    /**
     * Return the Element data tree
     *
     * @Route("/teaserreference", name="tree_teaserreference")
     */
    public function teaserreferenceAction()
    {
        $container = $this->getContainer();

        // TODO: switch to master language of element
        $defaultLanguage = $container->getParameter('phlexible_cms.languages.default');

        $filters = array(
            '*' => array('StringTrim', 'StripTags'),
        );

        $validators = array(
            'node' => array(
                'Int',
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
            ),
            'siteroot_id' => array(
                'Uuid',
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
            ),
            'language' => array(
                array('StringLength', 2, 2),
                array('InArray', $container->getParameter('frontend.languages.available')),
                'default' => $defaultLanguage
            ),
        );

        $fi = new Brainbits_Filter_Input($filters, $validators, $this->_getAllParams());

        if (!$fi->isValid())
        {
            throw new Brainbits_Filter_Exception('Error occured', 0, $fi);
        }

        $siterootID = $fi->siteroot_id;
        $tid        = $fi->node;
        $language   = $fi->language;

        $treeManager    = Makeweb_Elements_Tree_Manager::getInstance();
        $elementManager = Makeweb_Elements_Element_Manager::getInstance();

        $tree = $treeManager->getBySiteRootID($siterootID);

        $rootNode = $tree->getRoot();

        $data = array();
        if ($rootNode !== null)
        {
            if ($tid === null || $tid < 1)
            {
                $data = array($this->_getNodeData($rootNode, $language));
                $this->getResponse()->setAjaxPayload($data);

                return;
            }

            $node  = $tree->getNode($tid);
            $nodes = $tree->getChildren($tid);

            $data  = $this->_recurseNodes($nodes, $language);

            foreach ($data as $key => $row)
            {
                if ($row['leaf'])
                {
                    unset($data[$key]);
                    continue;
                }
            }

            $data[$key]['cls'] = (!empty($data[$key]['cls']) ? $data[$key]['cls'] . ' ' : '') . 'node-disabled';
        }

        $currentTreeId = $tid;

        $elementManager     = Makeweb_Elements_Element_Manager::getInstance();
        $teaserManager      = Makeweb_Teasers_Manager::getInstance();
        $elementTypeManager = Makeweb_Elementtypes_Elementtype_Manager::getInstance();
        $treeManager        = Makeweb_Elements_Tree_Manager::getInstance();
        $layoutAreaManager  = Makeweb_Teasers_Layoutarea_Manager::getInstance();

        $element    = $elementManager->getByEID($node->getEid());

        $layoutAreas = $layoutAreaManager->getFor($element->getElementTypeID());

        foreach ($layoutAreas as $layoutArea)
        {
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
                'qtip'       => $this->getContainer()->get('translator')->trans('elements.doubleclick_to_sort', array(), 'gui'),
            );

            $teasers = $teaserManager->getAllByTID($currentTreeId, $layoutArea->getID(), $language, false, array(), true);

            foreach ($teasers as $teaserArray)
            {
                switch ($teaserArray['type'])
                {
                    case 'teaser':
                        $teaser = $teaserManager->getByEID($teaserArray['teaser_eid']);
                        $teaserNode = new Makeweb_Teasers_Node($teaserArray['id']);

                        $areaRoot['children'][] = array(
                            'id'            => $teaserArray['id'],
                            'parent_tid'    => $currentTreeId,
                            'parent_eid'    => $element->getEid(),
                            'layoutarea_id' => $layoutArea->getID(),
                            'icon'          => $teaser->getIconUrl($teaserArray->getIconParams($language)),
                            'text'          => $teaser->getBackendTitle($language, $element->getMasterLanguage()), // . ' [' . $teaser->getEid() . ']',
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

            if (count($areaRoot['children']))
            {
                $data[] = $areaRoot;
            }
        }

        $data = array_values($data);

        $this->getResponse()->setAjaxPayload($data);
    }

    /**
     * Return the Element data tree
     *
     * @Route("/link", name="tree_link")
     */
    public function linkAction()
    {
        $container = $this->getContainer();

        $treeManager           = $container->get('phlexible_tree.manager');
        $elementManager        = $container->elementsManager;
        $elementVersionManager = $container->elementsVersionManager;

        $currentSiteRootID = $this->_getParam('siteroot_id', null);
        $id                = $this->_getParam('node', 'root');
        $language          = $this->_getParam('language', null);
        $recursive         = (boolean) $this->_getParam('recursive', false);

        if (null === $language)
        {
            if ($id != 'root')
            {
                $node = $treeManager->getNodeByNodeID($id);
            }
            else
            {
                $rootId = $treeManager->getRootNodeId($currentSiteRootID);
                $node = $treeManager->getNodeByNodeID($rootId);
            }
            $element  = $elementManager->getByEID($node->getEid());
            $language = $element->getMasterLanguage();
        }

        if ($id == 'root')
        {
            $siteRootManager = Makeweb_Siteroots_Siteroot_Manager::getInstance();
            $siteRoots       = $siteRootManager->getAllSiteRoots();

            // move current siteroot to the beginning
            if ($currentSiteRootID !== null)
            {
                array_unshift($siteRoots, $siteRoots[$currentSiteRootID]);
                unset($siteRoots[$currentSiteRootID]);
            }

            $data = array();
            foreach ($siteRoots as $siteRoot)
            {
                $siteRootID = $siteRoot->getId();
                $tree       = $treeManager->getBySiteRootID($siteRootID);
                $rootNode   = $tree->getRoot();

                $elementVersion = $elementVersionManager->getLatest($rootNode->getEID());

                $data[] = array(
                    'id'       => $rootNode->getID(),
                    'eid'      => $rootNode->getEID(),
                    'text'     => $siteRoot->getTitle(),
                    'icon'     => $elementVersion->getIconUrl(),
    //                'cls'      => 'siteroot-node',
    //                'children' => $startNode->hasChildren() ? $this->_recurseNodes($startNode->getChildren(), $language) : array(),
                    'leaf'     => !$rootNode->hasChildren(),
                    'expanded' => $siteRootID == $currentSiteRootID,
                );
            }
        }
        else
        {
            $node  = $treeManager->getNodeByNodeID($id);
            $nodes = $node->getChildren();
            $data  = $this->_recurseLinkNodes($nodes, $language, $recursive);
        }

        $this->getResponse()->setAjaxPayload($data);
    }

    /**
     * Return the Element data tree
     *
     * @Route("/linkelement", name="tree_linkelement")
     */
    public function linkelementAction()
    {
        $container = $this->getContainer();

        $treeManager    = $container->get('phlexible_tree.manager');
        $elementManager = $container->elementsManager;

        $siterootID = $this->_getParam('siteroot_id');
        $id         = $this->_getParam('node', 'root');
        $language   = $this->_getParam('language', null);
        $targetTid  = $this->_getParam('value', null);

        if (!$language)
        {
            if ($id != 'root')
            {
                $node = $treeManager->getNodeByNodeID($id);
            }
            else
            {
                $rootId = $treeManager->getRootNodeId($siterootID);
                $node = $treeManager->getNodeByNodeID($rootId);
            }

            $element  = $elementManager->getByEID($node->getEid());
            $language = $element->getMasterLanguage();
        }

        $elementTypeIds = $this->_getParam('element_type_ids', array());

        if ($elementTypeIds)
        {
            $elementTypeIds = explode(',', $elementTypeIds);
        }
        else
        {
            $elementTypeIds = array();
        }

        $tree = $treeManager->getBySiteRootID($siterootID);
        if ($id == 'root')
        {
            $startNode = $tree->getRootNode();
        }
        else
        {
            $startNode = $treeManager->getNodeByNodeId($id);
        }

        $targetNode = null;
        if ($targetTid)
        {
            $targetNode = $treeManager->getNodeByNodeId($targetTid);
        }

        if (!count($elementTypeIds))
        {
            $mode = !$targetTid ? self::MODE_NOET_NOTARGET : self::MODE_NOET_TARGET;

            $nodes = $startNode->getChildren();
            $data = $this->_recurseLinkNodes($nodes, $language, $mode, $targetNode);
        }
        else
        {
            $mode = !$targetTid ? self::MODE_ET_NOTARGET : self::MODE_ET_TARGET;

            $data = $this->_findLinkNodes($startNode->getSiteRootId(), $language, $elementTypeIds);

            if ($elementTypeIds)
            {
                $data = $this->_recursiveTreeStrip($data);
            }
        }

        $this->getResponse()->setAjaxPayload($data);
    }

    /**
     * Strip all disabled nodes recursivly
     *
     * @param  array $data
     * @return array
     */
    protected function _recursiveTreeStrip(array $data)
    {
        if (count($data) === 1 && !empty($data[0]['children']))
        {
            return $this->_recursiveTreeStrip($data[0]['children']);
        }

        return $data;
    }

    /**
     * @Route("/linkintrasiteroot", name="tree_linkintrasiteroot")
     */
    public function linkintrasiterootAction()
    {
        $container = $this->getContainer();

        $treeManager           = $container->get('phlexible_tree.manager');
        $elementVersionManager = $container->elementsVersionManager;

        // TODO: switch to master language of element
        $defaultLanguage = $container->getParam(':phlexible_cms.languages.default');

        $siterootID     = $this->_getParam('siteroot_id', null);
        $id             = $this->_getParam('node', 'root');
        $recursive      = (boolean) $this->_getParam('recursive', false);
        $language       = $this->_getParam('language', null);
        $elementTypeIds = $this->_getParam('element_type_ids', array());
        $targetTid      = $this->_getParam('value', null);

        if (!$language)
        {
            $language = $defaultLanguage;
        }

        if ($elementTypeIds)
        {
            $elementTypeIds = explode(',', $elementTypeIds);
        }
        else
        {
            $elementTypeIds = array();
        }

        $targetNode = null;
        if ($targetTid)
        {
            $targetNode = $treeManager->getNodeByNodeID($targetTid);
        }

        if ($id == 'root')
        {
            $siteRootManager = Makeweb_Siteroots_Siteroot_Manager::getInstance();
            $siteRoots       = $siteRootManager->getAllSiteRoots();

            if ($siterootID !== null)
            {
                unset($siteRoots[$siterootID]);
            }

            $data = array();
            foreach ($siteRoots as $siteRootID => $siteRoot)
            {
                $tree     = $treeManager->getBySiteRootID($siteRootID);
                $rootNode = $tree->getRoot();

                $elementVersion = $elementVersionManager->getLatest($rootNode->getEID());

                $children = false;
                if ($targetNode && $siteRootID === $targetNode->getSiteRootId())
                {
                    if (!count($elementTypeIds))
                    {
                        $mode = !$targetTid ? self::MODE_NOET_NOTARGET : self::MODE_NOET_TARGET;

                        $nodes    = $rootNode->getChildren();
                        $children = $this->_recurseLinkNodes($nodes, $language, $mode, $targetNode);
                    }
                    else
                    {
                        $mode = !$targetTid ? self::MODE_ET_NOTARGET : self::MODE_ET_TARGET;

                        $children = $this->_findLinkNodes($rootNode->getSiteRootId(), $language, $elementTypeIds);

                        if ($elementTypeIds)
                        {
                            $children = $this->_recursiveTreeStrip($children);
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
        }
        else
        {
            $startNode = $treeManager->getNodeByNodeID($id);

            if (!count($elementTypeIds))
            {
                $mode = !$targetTid ? self::MODE_NOET_NOTARGET : self::MODE_NOET_TARGET;

                $nodes = $startNode->getChildren();
                $data  = $this->_recurseLinkNodes($nodes, $language, $mode, $targetNode);
            }
            else
            {
                $mode = !$targetTid ? self::MODE_ET_NOTARGET : self::MODE_ET_TARGET;

                $data = $this->_findLinkNodes($startNode->getSiteRootId(), $language, $elementTypeIds);

                if ($elementTypeIds)
                {
                    $data = $this->_recursiveTreeStrip($data);
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

        try
        {
            $db = $container->dbPool->default;
            $db->beginTransaction();

            $filters = array(
                '*' => array('StringTrim', 'StripTags'),
            );

            $validators = array(
                'id' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'siteroot_id' => array(
                    'Uuid',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'element_type_id' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'prev_id' => array(
                    'Int',
                    Zend_Filter_Input::DEFAULT_VALUE => 0,
                ),
                'sort' => array(
                    'Alpha',
                    Zend_Filter_Input::ALLOW_EMPTY   => true,
                    Zend_Filter_Input::DEFAULT_VALUE => 'free',
                ),
                'navigation' => array(
                    'Alpha',
                    Zend_Filter_Input::ALLOW_EMPTY   => true,
                    Zend_Filter_Input::DEFAULT_VALUE => '',
                ),
                'restricted' => array(
                    'Alpha',
                    Zend_Filter_Input::ALLOW_EMPTY   => true,
                    Zend_Filter_Input::DEFAULT_VALUE => '',
                ),
                'masterlanguage' => array(
                    'Alpha',
                    Zend_Filter_Input::PRESENCE      => Zend_Filter_Input::PRESENCE_REQUIRED,
                    Zend_Filter_Input::DEFAULT_VALUE => $container->getParam(':frontend.languages.frontend')
                )
            );

            $fi = new Brainbits_Filter_Input($filters, $validators, $this->_getAllParams());

            if (!$fi->isValid())
            {
                throw new Brainbits_Filter_Exception('Error occured', 0, $fi);
            }

            $parentId       = $fi->id;
            $siterootID     = $fi->siteroot_id;
            $elementTypeID  = $fi->element_type_id;
            $prevId         = $fi->prev_id;
            $sortMode       = $fi->sort;
            $navigation     = $fi->navigation ? true : false;
            $restricted     = $fi->restricted ? true : false;
            $masterLanguage = $fi->masterlanguage;

            $elementManager = Makeweb_Elements_Element_Manager::getInstance();
            $treeManager    = Makeweb_Elements_Tree_Manager::getInstance();
            $tree           = $treeManager->getBySiteRootId($siterootID);

            $newElement        = $elementManager->create($elementTypeID, true, $masterLanguage);
            $newEid            = $newElement->getEid();
            $newElementVersion = $newElement->getLatestVersion();

            // $siterootID, $parentId, $prevId, $navigation, $restricted

            // place new element in element_tree
            $newTreeId  = $tree->add($parentId, $newEid, $prevId, 'element', $sortMode);

            if ($navigation !== null || $restricted !== null)
            {
                $tree->setPage($newTreeId,
                               $newElementVersion->getVersion(),
                               $navigation,
                               $restricted);
            }

            $db->commit();

            $result = MWF_Ext_Result::encode(
                true,
                $newEid,
                'Element EID "'.$newEid.' (' . $masterLanguage . ')" created.',
                array(
                    'eid'             => $newEid,
                    'tid'             => $newTreeId,
                    'master_language' => $masterLanguage,
                    'navigation'      => $navigation,
                    'restricted'      => $restricted
                )
            );
        }
        catch (Exception $e)
        {
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
        try
        {
            $db = $this->getContainer()->dbPool->default;
            $db->beginTransaction();

            $filters = array(
                '*' => array('StringTrim', 'StripTags'),
            );

            $validators = array(
                'id' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'for_tree_id' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'prev_id' => array(
                    'Int',
                    Zend_Filter_Input::DEFAULT_VALUE => 0,
                ),
            );

            $fi = new Brainbits_Filter_Input($filters, $validators, $this->_getAllParams());

            if (!$fi->isValid())
            {
                throw new Brainbits_Filter_Exception('Error occured', 0, $fi);
            }

            $parentId   = $fi->id;
            $instanceId = $fi->for_tree_id;
            $prevId     = $fi->prev_id;

            $manager = Makeweb_Elements_Tree_Manager::getInstance();

            $targetNode = $manager->getNodeByNodeID($parentId);
            $tree       = $targetNode->getTree();

            $tree->createAlias($parentId, $instanceId, $prevId);

            $db->commit();

            $result = MWF_Ext_Result::encode(true, 0, 'Alias created.');
        }
        catch (Exception $e)
        {
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
        try
        {
            $db = $this->getContainer()->dbPool->default;
            $db->beginTransaction();

            $filters = array(
                '*' => array('StringTrim', 'StripTags'),
            );

            $validators = array(
                'id' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'for_tree_id' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'prev_id' => array(
                    'Int',
                    Zend_Filter_Input::ALLOW_EMPTY => true,
                    Zend_Filter_Input::DEFAULT_VALUE => 0,
                ),
            );

            $fi = new Brainbits_Filter_Input($filters, $validators, $this->_getAllParams());

            if (!$fi->isValid())
            {
                throw new Brainbits_Filter_Exception('Error occured', 0, $fi);
            }

            $parentId = $fi->id;
            $sourceId = $fi->for_tree_id;
            $prevId   = $fi->prev_id;

            $elementManager = Makeweb_Elements_Element_Manager::getInstance();
            $treeManager    = Makeweb_Elements_Tree_Manager::getInstance();

            $sourceNode = $treeManager->getNodeByNodeID($sourceId);
            $tree       = $sourceNode->getTree();
            $sourceEid  = $sourceNode->getEid();

            $select = $db->select()
                         ->from($db->prefix . 'element', array('element_type_id', 'masterlanguage'))
                         ->where('eid = ?', $sourceEid);

            $sourceElementRow = $db->fetchRow($select);

            $targetElement        = $elementManager->create($sourceElementRow['element_type_id'], false, $sourceElementRow['masterlanguage']);
            $targetEid            = $targetElement->getEid();

            // place new element in element_tree
            $targetId = $tree->add($parentId, $targetEid, $prevId);

            // copy element version data
            $sourceElement        = $elementManager->getByEID($sourceEid);
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
        }
        catch (Exception $e)
        {
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
        try
        {
            $filters = array('StringTrim', 'StripTags');

            $validators = array(
                'id' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
                'target' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                )
            );

            $fi = new Brainbits_Filter_Input($filters, $validators, $this->_getAllParams());

            if (!$fi->isValid())
            {
                throw new Brainbits_Filter_Exception('Error occured', 0, $fi);
            }

            $id       = $fi->id;
            $targetId = $fi->target;

            $treeManager = Makeweb_Elements_Tree_Manager::getInstance();
            $node = $treeManager->getNodeByNodeID($id);
            $tree = $node->getTree();

            if ($id === $targetId)
            {
                $result = MWF_Ext_Result::encode(false, null, 'source_id === target_id');
            }
            else
            {
                $tree->move($id, $targetId);

                $result = MWF_Ext_Result::encode(true, $id, 'Element moved.', array('id' => $id, 'parent_id' => $targetId));
            }
        }
        catch (Exception $e)
        {
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
        try
        {
            $filters = array('StringTrim', 'StripTags');

            $validators = array(
                'id' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
            );

            $fi = new Brainbits_Filter_Input($filters, $validators, $this->_getAllParams());

            if (!$fi->isValid())
            {
                throw new Brainbits_Filter_Exception('Error occured', 0, $fi);
            }

            $treeId = $fi->id;

            $container   = $this->getContainer();
            $treeManager = $container->get('phlexible_tree.manager');
            $db          = $container->dbPool->read;

            $nodeId = $treeId[0];
            $node   = $treeManager->getNodeByNodeId($nodeId);

            $sql = $db->select()
                ->from(
                    $db->prefix . 'element_tree',
                    array('id', 'siteroot_id', 'modify_time', 'instance_master')
                )
                ->where('eid = ?', $node->getEid());
            $instances = $db->fetchAll($sql);

            if (count($instances) > 1)
            {
                $siterootManager = $container->siterootManager;
                $treeHelper      = $container->elementsTreeTreeHelper;

                $instancesArray = array();
                foreach ($instances as $instance)
                {
                    $siteroot      = $siterootManager->getById($instance['siteroot_id']);
                    $instanceNode  = $treeManager->getNodeByNodeId($instance['id']);
                    $instanceTitle = $treeHelper->getOnlineTitleByTid($instanceNode->getParentId(), 'de');

                    $instancesArray[] = array(
                        $instance['id'],
                        $siteroot->getTitle(),
                        $instanceTitle,
                        $instance['modify_time'],
                        (boolean) $instance['instance_master'],
                        (boolean) ($instance['id'] == $nodeId)
                    );
                }

                $result = MWF_Ext_Result::encode(true, $nodeId, '', $instancesArray);
            }
            else
            {
                $result = MWF_Ext_Result::encode(true, $nodeId, '', array());
            }

        }
        catch (Makeweb_Elements_Tree_Exception_LockException $e)
        {
            $result = MWF_Ext_Result::encode(false, $treeId, $e->getMessage());
        }
        catch (Exception $e)
        {
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
        try
        {
            $filters = array('StringTrim', 'StripTags');

            $validators = array(
                'id' => array(
                    'Int',
                    Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
                ),
            );

            $fi = new Brainbits_Filter_Input($filters, $validators, $this->_getAllParams());

            if (!$fi->isValid())
            {
                throw new Brainbits_Filter_Exception('Error occured', 0, $fi);
            }

            $treeIds = $fi->id;
            if (!is_array($treeIds))
            {
                $treeIds = array($treeIds);
            }

            $container   = $this->getContainer();
            $db          = $container->dbPool->write;
            $treeManager = $container->elementsTreeManager;

            $db->beginTransaction();

            foreach ($treeIds as $treeId)
            {
                $tree = $treeManager->getByNodeId($treeId);
                $tree->delete($treeId);
            }

            //$fileUsage = new Makeweb_Elements_Element_FileUsage(MWF_Registry::getContainer()->dbPool);
            //$fileUsage->update($eid);

            $db->commit();

            $result = MWF_Ext_Result::encode(true, $treeId, 'Item(s) deleted.');
        }
        catch (Makeweb_Elements_Tree_Exception_LockException $e)
        {
            $db->rollBack();
            $result = MWF_Ext_Result::encode(false, $treeId, $e->getMessage());
        }
        catch (Exception $e)
        {
            $db->rollBack();
            $result = MWF_Ext_Result::exception($e);
        }

        $this->getResponse()->setAjaxPayload($result);
    }
}
