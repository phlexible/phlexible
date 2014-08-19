<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $language = $request->get('language');
        $query = $request->get('query');

        $elementService = $this->get('phlexible_element.service');
        $treeManager = $this->get('phlexible_tree.manager');

        $select = $db->select()
            ->distinct()
            ->from(array('et' => $db->prefix . 'element_tree'), array('id AS tid'))
            ->join(array('e' => $db->prefix . 'element'), 'et.eid = e.eid', array('latest_version AS version'))
            ->join(
                array('evt' => $db->prefix . 'element_version_titles'),
                'evt.eid = et.eid AND evt.version = e.latest_version AND evt.language = ' . $db->quote(
                    $language
                ) . ' AND evt.backend LIKE ' . $db->quote('%' . $query . '%'),
                array('backend AS title')
            )
            ->where('et.siteroot_id = ?', $siterootId);

        $result = $db->fetchAll($select);

        foreach ($result as $key => $row) {
            $node = $treeManager->getNodeByNodeId($row['tid']);
            $element = $elementService->findElement($row['eid']);
            $elementVersion = $elementService->findLatestElementVersion($element);
            $icon = '';//$elementVersion->getIconUrl($node->getIconParams($language));
            $result[$key]['icon'] = $icon;
        }

        return new JsonResponse(array('results' => $result));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/media", name="elements_search_media")
     */
    public function mediaAction(Request $request)
    {
        $query = $request->get('query');

        // TODO: meta search

        $results = array();
        foreach ($this->get('phlexible_media_site.site_manager')->getAll() as $site) {
            $files = $site->search($query);

            foreach ($files as $file) {
                /* @var $file FileInterface */

                $results[] = array(
                    'id'        => $file->getId(),
                    'version'   => $file->getVersion(),
                    'name'      => $file->getName(),
                    'folder_id' => $file->getFolderId(),
                );
            }
        }
        return new JsonResponse(array('results' => $results));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/medialink", name="elements_search_medialink")
     */
    public function medialinkAction(Request $request)
    {
        $fileId = $request->get('file_id');

        $site = $this->get('phlexible_media_site.site_manager')->getByFileId($fileId);
        $file = $site->findFile($fileId);
        $urls = $site->getStorageDriver()->getUrls($file);

        return new JsonResponse($urls);
    }
}
