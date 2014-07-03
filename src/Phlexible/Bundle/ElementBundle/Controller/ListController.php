<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

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
 * @Route("/elements/list")
 * @Security("is_granted('elements')")
 */
class ListController extends Controller
{
    /**
     * List all Elements
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="elements_list")
     */
    public function listAction(Request $request)
    {
        $start = $request->get('start', 0);
        $limit = $request->get('limit', 25);
        $sort = $request->get('sort');
        $dir = $request->get('dir');
        $tid = $request->get('tid');
        $language = $request->get('language');
        $filterValues = $request->get('filter');
        if ($filterValues) {
            $filterValues = json_decode($filterValues, true);
        } else {
            $filterValues = array();
        }

        $data = array();

        $dispatcher = $this->get('event_dispatcher');
        $treeManager = $this->get('phlexible_tree.manager');
        $elementManager = $this->get('elementsManager');
        $elementVersionManager = $this->get('elementsVersionManager');
        $node = $treeManager->getNodeByNodeID($tid);
        $tree = $node->getTree();
        $eid = $node->getEid();
        $element = $elementManager->getByEID($eid);
        $elementMasterLanguage = $element->getMasterLanguage();
        $elementVersion = $element->getLatestVersion();
        $elementTypeVersion = $elementVersion->getElementTypeVersionObj();
        //$elementData = $element->getData(0, 'en');

        if (!$language) {
            $language = $elementMasterLanguage;
        }

        $rightsIdentifiers = array(
            array('uid' => $this->getUser()->getId())
        );
        foreach ($this->getUser()->getGroups() as $group) {
            $rightsIdentifiers[] = array('gid' => $group->getId());
        }

        $contentRightsManager = $this->getContainer()->get('contentRightsManager');

        $userAdminRights = null;
        if (MWF_Env::getUser()->isAllowed(MWF_Core_Acl_Acl::RESOURCE_SUPERADMIN) ||
            MWF_Env::getUser()->isAllowed(MWF_Core_Acl_Acl::RESOURCE_DEVELOPMENT)
        ) {
            $contentRightsHelper = $container->contentRightsHelper;
            $userAdminRights = array_keys($contentRightsHelper->getRights('internal', 'treenode'));
        }

        /* Parent node */

        if (!$userAdminRights) {
            $contentRightsManager->calculateRights('internal', $node, $rightsIdentifiers);

            if (!$contentRightsManager->hasRight('VIEW', $language)) {
                $this->getResponse()->setAjaxPayload(
                    array(
                        'parent' => null,
                        'list'   => array(),
                        'total'  => 0
                    )
                );

                return;
            }

            $userRights = array_keys($contentRightsManager->getRights($language));
        } else {
            $userRights = $userAdminRights;
        }

        $parent = array(
            'tid'             => (int) $tid,
            'teaser_id'       => (int) 0,
            'eid'             => (int) $eid,
            'title'           => $elementVersion->getBackendTitle($language, $elementMasterLanguage),
            'element_type_id' => (int) $elementTypeVersion->getID(),
            'element_type'    => $elementTypeVersion->getTitle(),
            'icon'            => $elementVersion->getIconUrl($node->getIconParams($language)),
            'author'          => 'author',
            'version'         => $elementVersion->getVersion(),
            'create_time'     => $elementVersion->getCreateTime(),
            //            'change_time'     => '2007-01-01 01:01:01',
            'publish_time'    => $node->getPublishDate($language),
            'custom_date'     => $elementVersion->getCustomDate($language),
            'language'        => $language,
            'sort'            => 0,
            'sort_mode'       => $node->getSortMode(),
            'sort_dir'        => $node->getSortDir(),
            'version_latest'  => (int) $node->getLatestVersion(),
            'version_online'  => (int) $node->getOnlineVersion($language),
            'status'          => ' o_O ',
            'rights'          => $userRights,
            'qtip'            =>
                $elementTypeVersion->getTitle() . ', Version ' . $elementTypeVersion->getVersion() . '<br>' .
                'Version ' . $elementVersion->getVersion() . '<br>' .
                37 . ' Versions<br>'
        );

        $filter = new Makeweb_Elements_Tree_Filter(
            $this->getContainer()->dbPool,
            $this->getContainer()->dispatcher,
            $node->getId(),
            $language
        );
        $filter->setFilterValues($filterValues)
            ->setSortMode($sort)
            ->setSortDir($dir);

        $childIds = $filter->getIds($limit, $start);
        $cnt = $filter->getCount();

        $data = array();
        foreach ($childIds as $childId => $latestVersion) {
            $childNode = $tree->getNode($childId);

            if (!$userAdminRights) {
                $contentRightsManager->calculateRights('internal', $childNode, $rightsIdentifiers);

                if (!$contentRightsManager->hasRight('VIEW', $language)) {
                    continue;
                }

                $userRights = array_keys($contentRightsManager->getRights($language));
            } else {
                $userRights = $userAdminRights;
            }

            $childElementVersion = $elementVersionManager->getLatest($childNode->getEid(), $latestVersion);
            $childElement = $childElementVersion->getElement();
            $childTitle = $childElementVersion->getBackendTitle($language, $childElement->getMasterLanguage());
            $childElementTypeVersion = $childElementVersion->getElementTypeVersionObj();

            $data[] = array(
                'tid'             => (int) $childNode->getId(),
                'eid'             => (int) $childNode->getEid(),
                '_type'           => 'element',
                'title'           => $childTitle,
                'element_type_id' => (int) $childElementTypeVersion->getID(),
                'element_type'    => $childElementTypeVersion->getTitle(),
                'navigation'      => $childNode->inNavigation($childElementVersion->getVersion()),
                'restricted'      => $childNode->isRestricted($childElementVersion->getVersion()),
                'icon'            => $childElementVersion->getIconUrl($childNode->getIconParams($language)),
                'author'          => 'author',
                'version'         => $childElementVersion->getVersion(),
                'create_time'     => $childNode->getCreateDate(),
                //                'change_time'     => $child['modify_time'],
                'publish_time'    => $childNode->getPublishDate($language),
                'custom_date'     => $childElementVersion->getCustomDate($language),
                'language'        => $language,
                'sort'            => (int) $childNode->getSort(),
                'version_latest'  => (int) $childNode->getLatestVersion(),
                'version_online'  => (int) $childNode->getOnlineVersion($language),
                'status'          => '>o>',
                'rights'          => $userRights,
                'qtip'            => $childElementTypeVersion->getTitle(
                    ) . ', Version ' . $childElementTypeVersion->getVersion() . '<br>' .
                    'Version ' . $childElementVersion->getVersion() . '<br>',
            );
        }

        //$data['totalChilds'] = $element->getChildCount();

        return new JsonResponse(array(
            'parent' => $parent,
            'list'   => $data,
            'total'  => $cnt
        ));
    }

    /**
     * Node reordering
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/sort", name="elements_list_sort")
     */
    public function sortAction(Request $request)
    {
        $tid = $request->get('tid');
        $mode = $request->get('mode');
        $dir = strtolower($request->get('dir'));
        $sortTids = $request->get('sort_ids');
        $sortTids = json_decode($sortTids, true);

        $container = $this->getContainer();

        $treeManager = $container->get('phlexible_tree.manager');
        $treeSorter = $container->elementsTreeSorter;

        $tree = $treeManager->getByNodeId($tid);
        $tree->setSortMode($tid, $mode, $dir);

        if ($mode == Makeweb_Elements_Tree::SORT_MODE_FREE) {
            $tree->reorder($tid, $sortTids);
        } else {
            $node = $tree->getNode($tid);
            $treeSorter->sortNode($node);
        }

        return new ResultResponse(true, 'Tree sort published.');
    }
}
