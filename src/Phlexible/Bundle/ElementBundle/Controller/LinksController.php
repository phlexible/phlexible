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

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $tree = $treeManager->getByNodeId($tid);
        $node = $tree->get($tid);

        $linkRepository = $this->getDoctrine()->getRepository('PhlexibleElementBundle:ElementLink');

        $result = array();

        if ($incoming) {
            $links = $linkRepository->findBy(array('type' => 'treeNode', 'target' => $node->getTypeId()));
        } else {
            $links = $linkRepository->findBy(array('eid' => $node->getTypeId()));
        }

        foreach ($links as $link) {
            $result[] = array(
                'id'      => $link->getId(),
                'iconCls' => 'p-element-component-icon',
                'type'    => $link->getType(),
                'title'   => $link->getType() . ' ' . $link->getTarget(),
                'content' => 'content',
                'link'    => array(),
                'raw'     => 'raw'
            );
        }

        return new JsonResponse(array('links' => $result));
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
