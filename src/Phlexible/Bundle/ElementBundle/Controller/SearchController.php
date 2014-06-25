<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Search controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/search")
 * @Security("is_granted('elements')")
 */
class SearchController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/elements", name="elements_search_elements")
     */
    public function elementsAction(Request $request)
    {
        $siterootId = $request->get('siteroot_id');
        $language   = $request->get('language');
        $query      = $request->get('query');

        $container = $this->getContainer();
        $db = $container->dbPool->read;
        $elementVersionManager = $container->elementsVersionManager;
        $treeManager = $container->get('phlexible_tree.manager');

        $select = $db->select()
            ->distinct()
            ->from(array('et' => $db->prefix . 'element_tree'), array('id AS tid'))
            ->join(array('e' => $db->prefix . 'element'), 'et.eid = e.eid', array('latest_version AS version'))
            ->join(array('evt' => $db->prefix . 'element_version_titles'), 'evt.eid = et.eid AND evt.version = e.latest_version AND evt.language = ' . $db->quote($language) . ' AND evt.backend LIKE ' . $db->quote('%' . $query . '%'), array('backend AS title'))
            ->where('et.siteroot_id = ?', $siterootId);

        $result = $db->fetchAll($select);

        foreach ($result as $key => $row)
        {
            $node = $treeManager->getNodeByNodeId($row['tid']);
            $elementVersion = $elementVersionManager->getLatest($node->getEid());
            $icon = $elementVersion->getIconUrl($node->getIconParams($language));
            $result[$key]['icon'] = $icon;
        }

        return new JsonResponse(array('results' => $result));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/media", name="elements_rights_media")
     */
    public function mediaAction(Request $request)
    {
        $siterootId = $request->get('siteroot_id');
        $query      = $request->get('query');

        $db = $this->getContainer()->dbPool->read;

        $select1 = $db->select()
            ->from($db->prefix . 'mediamanager_files', array('id', 'version', 'name', 'folder_id'))
            ->where('name LIKE ?', '%' . $query . '%');

        $select2 = $db->select()
            ->from(array('f' => $db->prefix . 'mediamanager_files'), array('id', 'version', 'name', 'folder_id'))
            ->join(array('m' => $db->prefix . 'mediamanager_files_metasets_items'), 'm.file_id = f.id AND m.file_version = m.file_version', array())
            ->where('m.meta_value_de LIKE ?', '%' . $query . '%')
            ->orWhere('m.meta_value_en LIKE ?', '%' . $query . '%');
            #->order('m.meta_key');

        $select = $db->select()
            ->distinct()
            ->union(array($select1, $select2))
            ->limit(10);

        $result = $db->fetchAll($select);

        return new JsonResponse(array('results' => $result));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/medialink", name="elements_rights_medialink")
     */
    public function medialinkAction(Request $request)
    {
        $fileId = $request->get('file_id');

        $site = Media_Site_Manager::getInstance()->getByFileId($fileId);
        $file = $site->getFilePeer()->getByID($fileId);
        $urls = $site->getStorageDriver()->getUrls($file);

        return new JsonResponse($urls);
    }
}
