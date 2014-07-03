<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Links controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/links")
 * @Security("is_granted('elements')")
 */
class LinksController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/list", name="elements_links_list")
     */
    public function listAction(Request $request)
    {
        $tid = $request->get('tid');
        $language = $request->get('language');
        $version = $request->get('version');
        $incoming = $request->get('incoming', false);

        $displayLanguage = $language;

        $elementVersionManager = Makeweb_Elements_Element_Version_Manager::getInstance();
        $siteRootManager = Makeweb_Siteroots_Siteroot_Manager::getInstance();
        $treeManager = Makeweb_Elements_Tree_Manager::getInstance();

        $node = $treeManager->getNodeByNodeID($tid);

        $elementVersion = $elementVersionManager->get($node->getEid(), $version);
        $elementData = $elementVersion->getData($language);
        $data = $elementData->getWrap();

        $result = array();

        $rii = new RecursiveIteratorIterator($data, RecursiveIteratorIterator::SELF_FIRST);
        $i = 0;
        foreach ($rii as $node) {
            if (empty($node) || empty($node['content'])) {
                continue;
            }

            switch ($node['type']) {
                case 'image':
                case 'flash':
                case 'download':
                case 'video':
                    if (empty($node['media'])) {
                        continue;
                    }

                    $documentTypeRepository = MWF_Registry::getContainer()->get('documenttypes.repository');
                    $documentType = $documentTypeRepository->find($node['media']['documenttype']);

                    $imageUrl = $this->_request->getBaseUrl() . '/media/' . $node['media']['file_id'];
                    $content = '<img src="' . $imageUrl . '/_mm_medium" width="48" height="48" style="margin-right: 5px; float: left; padding: 1px; border: 1px solid lightgrey; vertical-align: top" /> '
                        . $node['media']['name'] . '<br />'
                        . $documentType->getTitle('en') . '<br />'
                        . $node['media']['readablesize'];

                    $site = Media_Site_Manager::getInstance()->getByFolderId($node['media']['folder_id']);
                    $folder = $site->getFolderPeer()->getById($node['media']['folder_id']);
                    $folderPath = $folder->getIdPath();
                    $menuItem = new MWF_Core_Menu_Item_Panel();
                    $menuItem->setPanel('Phlexible.mediamanager.MediamanagerPanel')
                        ->setParam('start_file_id', $node['media']['file_id'])
                        ->setParam('start_folder_path', $folderPath);

                    $result[] = array(
                        'id'      => $i++,
                        'iconCls' => 'm-frontendmediamanager-field_' . $node['type'] . '-icon',
                        'type'    => $node['type'],
                        'title'   => $node['name'][$displayLanguage] . ' (' . $node['working_title'] . ')',
                        'content' => $content,
                        'link'    => $menuItem->get(),
                        'raw'     => $node['content']
                    );

                    break;

                case 'folder':
                    if (empty($node['media'])) {
                        continue;
                    }

                    $content = '/' . $node['media']['folder_path'];
                    $content = Brainbits_Util_String::deleteTrailing('/', $content, 1);

                    $site = Media_Site_Manager::getInstance()->getByFolderId($node['media']['folder_id']);
                    $folder = $site->getFolderPeer()->getById($node['media']['folder_id']);
                    $folderPath = $folder->getIdPath();
                    $menuItem = new MWF_Core_Menu_Item_Panel();
                    $menuItem->setPanel('Phlexible.mediamanager.MediamanagerPanel')
                        ->setParam('start_folder_path', $folderPath);

                    $result[] = array(
                        'id'      => $i++,
                        'iconCls' => 'm-frontendmediamanager-field_folder-icon',
                        'type'    => $node['type'],
                        'title'   => $node['name'][$displayLanguage] . ' (' . $node['working_title'] . ')',
                        'content' => $content,
                        'link'    => $menuItem->get(),
                        'raw'     => $node['content']
                    );

                    break;

                case 'link':
                    if ($node['link']['type'] === 'eid' || $node['link']['type'] === 'intrasiteroot') {
                        $treeNode = $treeManager->getNodeByNodeId($node['link']['id']);
                        $siteRoot = $siteRootManager->getByID($node['link']['siteroot_id']);
                        $elementVersion = $elementVersionManager->getLatest($node['link']['eid']);

                        $iconUrl = $elementVersion->getIconUrl($treeNode->getIconParams($language));
                        $icon = '<img src="' . $iconUrl . '" width="18" height="18" style="vertical-align: middle;" />'
                            . $node['displayContent'];

                        $menuItem = new MWF_Core_Menu_Item_Panel();
                        $menuItem->setPanel('Makeweb.elements.MainPanel')
                            ->setIdentifier('Makeweb_elements_MainPanel_' . $siteRoot->getTitle($language))
                            ->setParam('id', $node['link']['id'])
                            ->setParam('siteroot_id', $node['link']['siteroot_id'])
                            ->setParam('title', $siteRoot->getTitle())
                            ->setParam('start_tid_path', '/' . implode('/', $treeNode->getPath()));

                        $link = $menuItem->get();
                    } else {
                        $link = null;
                        $content = $node['content'];
                    }

                    $result[] = array(
                        'id'      => $i++,
                        'iconCls' => 'm-fields-field_' . $node['type'] . '-icon',
                        'type'    => $node['type'],
                        'title'   => $node['name'][$displayLanguage] . ' (' . $node['working_title'] . ')',
                        'link'    => $link,
                        'content' => $content,
                        'raw'     => $node['content']
                    );

                    break;
            }
        }

        if ($incoming) {
            $db = $this->getContainer()->dbPool->read;

            $select = $db->select()
                ->distinct()
                ->from(array('edl' => $db->prefix . 'element_data_language'), array('edl.content'))
                ->join(
                    array('ed' => $db->prefix . 'element_data'),
                    'edl.eid = ed.eid AND edl.version = ed.version AND edl.data_id = ed.data_id',
                    array('ed.eid')
                )
                ->joinLeft(
                    array('et' => $db->prefix . 'element_tree'),
                    'et.eid = ed.eid',
                    array('id AS tid')
                )
                ->joinLeft(
                    array('ett' => $db->prefix . 'element_tree_teasers'),
                    'ett.teaser_eid = ed.eid',
                    array('id AS teaser_id')
                )
                ->where('edl.content LIKE ?', 'id:' . $tid . '%')
                ->orWhere('edl.content LIKE ?', 'sr:' . $tid . '%');

            $data = $db->fetchAll($select);

            foreach ($data as $row) {
                $elementVersion = $elementVersionManager->getLatest($row['eid']);

                if (!empty($row['tid'])) {
                    $treeNode = $treeManager->getNodeByNodeId($row['tid']);
                    $siteRoot = $siteRootManager->getByID($treeNode->getSiterootId());

                    $icon = '<img src="' . $elementVersion->getIconUrl($treeNode->getIconParams($language))
                        . '" width="18" height="18" style="vertical-align: middle;" />';

                    $content = $icon . ' ' . $elementVersion->getBackendTitle($language) . ' [' . $row['tid'] . ']';
                    $title = 'Incoming TreeNode link';

                    $menuItem = new MWF_Core_Menu_Item_Panel();
                    $menuItem->setPanel('Makeweb.elements.MainPanel')
                        ->setIdentifier('Makeweb_elements_MainPanel_' . $siteRoot->getTitle($language))
                        ->setParam('id', $treeNode->getId())
                        ->setParam('siteroot_id', $treeNode->getSiterootId())
                        ->setParam('title', $siteRoot->getTitle())
                        ->setParam('start_tid_path', '/' . implode('/', $treeNode->getPath()));

                    $link = $menuItem->get();
                } else {
                    $icon = '<img src="' . $elementVersion->getIconUrl() .
                        '" width="18" height="18" style="vertical-align: middle;" />';

                    $content = $icon . ' ' . $elementVersion->getBackendTitle($language)
                        . ' [' . $row['teaser_id'] . ']';
                    $title = 'Incoming Teaser Link';
                    $link = null;
                }

                $result[] = array(
                    'id'      => $i++,
                    'iconCls' => 'm-fields-field_link-icon',
                    'type'    => 'link',
                    'title'   => $title,
                    'content' => $content,
                    'link'    => $link,
                    'raw'     => $row['content']
                );
            }
        }

        $result = array('links' => $result);

        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/search", name="elements_links_search")
     */
    public function searchAction(Request $request)
    {
        // TODO: switch to master language of element
        $defaultLanguage = $this->container->getParameter('phlexible_cms.languages.default');

        $db = $this->dbPool->default;

        $language = $request->get('language', $defaultLanguage);
        $query = $request->get('query');
        $siterootId = $request->get('siteroot_id');
        $allowTid = $request->get('allow_tid');
        $allowIntrasiteroot = $request->get('allow_intrasiteroot');
        $elementTypeIds = $request->get('element_type_ids', '');
        if ($elementTypeIds) {
            $elementTypeIds = explode(',', $elementTypeIds);
            foreach ($elementTypeIds as $key => $elementTypeId) {
                $elementTypeIds[$key] = $db->quote($elementTypeId);
            }
            $elementTypeIds = implode(',', $elementTypeIds);
        }

        $where = false;
        if (!$allowTid || !$allowIntrasiteroot) {
            $where = array();

            if ($allowTid) {
                $where[] = 'et.siteroot_id = ' . $db->quote($siterootId);
            }

            if ($allowIntrasiteroot) {
                $where[] = 'et.siteroot_id != ' . $db->quote($siterootId);
            }
            $where = implode(' OR ', $where);
        }

        $select = $db->select()
            ->distinct()
            ->from(
                array('et' => $db->prefix . 'element_tree'),
                array('id', 'eid', 'siteroot_id')
            )
            ->join(
                array('evt' => $db->prefix . 'element_version_titles'),
                'evt.eid = et.eid AND language = ' . $db->quote($language),
                array('backend AS title')
            )
            ->join(
                array('e' => $db->prefix . 'element'),
                'evt.eid = e.eid AND evt.version = e.latest_version',
                array()
            )
            ->where('et.id = ?', $query)
            ->order('title ASC');

        if ($where) {
            $select->where($where);
        }

        if ($elementTypeIds) {
            $select->join(
                array('ev' => $db->prefix . 'element_version'),
                'et.eid = ev.eid AND ev.element_type_id IN (' . $elementTypeIds . ')',
                array()
            );
        }

        $results1 = $db->fetchAssoc($select);

        $select = $db->select()
            ->distinct()
            ->from(
                array('et' => $db->prefix . 'element_tree'),
                array('id', 'eid', 'siteroot_id')
            )
            ->join(
                array('evt' => $db->prefix . 'element_version_titles'),
                'evt.eid = et.eid AND language = ' . $db->quote($language),
                array('backend AS title')
            )
            ->join(
                array('e' => $db->prefix . 'element'),
                'evt.eid = e.eid AND evt.version = e.latest_version',
                array()
            )
            ->where('evt.backend LIKE ?', '%' . $query . '%')
            ->order('title ASC');

        if ($where) {
            $select->where($where);
        }

        if ($elementTypeIds) {
            $select->join(
                array('ev' => $db->prefix . 'element_version'),
                'et.eid = ev.eid AND ev.element_type_id IN (' . $elementTypeIds . ')',
                array()
            );
        }

        $results2 = $db->fetchAssoc($select);

        $results = array_merge($results1, $results2);

        $siterootManager = $container->siterootManager;
        $siteroots = $siterootManager->getAllSiteRoots();

        $data = array();
        foreach ($results as $row) {
            $data[] = array(
                'id'    => ($siterootId == $row['siteroot_id'] ? 'id' : 'sr') . ':' . $row['id'],
                'tid'   => $row['id'],
                'eid'   => $row['eid'],
                'title' => $siteroots[$row['siteroot_id']]->getTitle($language)
                    . ' :: ' . $row['title'] . ' [' . $row['id'] . ']',
            );
        }

        return new JsonResponse(array('results' => $data));
    }
}
