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
 * @Security("is_granted('ROLE_ELEMENTS')")
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
        $elementService = $this->get('phlexible_element.element_service');
        $linkRepository = $this->getDoctrine()->getRepository('PhlexibleElementBundle:ElementLink');

        $tree = $treeManager->getByNodeId($tid);
        $node = $tree->get($tid);

        $element = $elementService->findElement($node->getTypeId());
        $elementVersion = $elementService->findElementVersion($element, $version);

        $result = array();

        if ($incoming) {
            $links = $linkRepository->findBy(array('type' => 'link-internal', 'target' => $node->getId()));
        } else {
            $links = $linkRepository->findBy(array('elementVersion' => $elementVersion));
        }

        foreach ($links as $link) {
            $result[] = array(
                'id'      => $link->getId(),
                'iconCls' => 'p-element-component-icon',
                'type'    => $link->getType(),
                'title'   => $link->getField(),
                'content' => $link->getTarget(),
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

        $language = $request->get('language', $defaultLanguage);
        $query = $request->get('query');
        $siterootId = $request->get('siteroot_id');
        $allowTid = $request->get('allow_tid');
        $allowIntrasiteroot = $request->get('allow_intrasiteroot');
        $elementTypeIds = $request->get('element_type_ids', '');

        $conn = $this->get('doctrine.dbal.default_connection');

        $qb = $conn->createQueryBuilder();

        if ($elementTypeIds) {
            $elementTypeIds = explode(',', $elementTypeIds);
            foreach ($elementTypeIds as $key => $elementTypeId) {
                $elementTypeIds[$key] = $qb->expr()->literal($elementTypeId);
            }
            $elementTypeIds = implode(',', $elementTypeIds);
        }

        $or = null;
        if (!$allowTid || !$allowIntrasiteroot) {
            if ($allowTid) {
                $where[] = $qb->expr()->eq('et.siteroot_id', $qb->expr()->literal($siterootId));
            }

            if ($allowIntrasiteroot) {
                $where[] = $qb->expr()->neq('et.siteroot_id', $qb->expr()->literal($siterootId));
            }

            $or = $qb->expr()->orX($where);
        }

        $qb
            ->select('t.id', 't.type_id AS eid', 't.siteroot_id', 'evmf.backend AS title')
            ->from('tree', 't')
            ->join('t', 'element', 'e', 't.type_id = e.eid')
            ->join('e', 'element_version', 'ev', 'e.eid = ev.eid AND e.latest_version = ev.version')
            ->join('ev', 'element_version_mapped_field', 'evmf', 'evmf.element_version_id = ev.id AND evmf.language = ' . $qb->expr()->literal($language))
            ->where($qb->expr()->eq('t.id', $qb->expr()->literal($query)))
            ->orderBy('title', 'ASC');

        if ($or) {
            $qb->andWhere($or);
        }

        if ($elementTypeIds) {
            $qb->join('e', 'element_version', 'ev', 'e.eid = ev.eid AND ev.element_type_id IN (' . $elementTypeIds . ')');
        }

        $results1 = $conn->fetchAll($qb->getSQL());

        $qb = $conn->createQueryBuilder();
        $qb
            ->select('t.id', 't.type_id AS eid', 't.siteroot_id', 'evmf.backend AS title')
            ->from('tree', 't')
            ->join('t', 'element', 'e', 't.type_id = e.eid')
            ->join('e', 'element_version', 'ev', 'e.eid = ev.eid AND e.latest_version = ev.version')
            ->join('ev', 'element_version_mapped_field', 'evmf', 'evmf.element_version_id = ev.id AND evmf.language = ' . $qb->expr()->literal($language))
            ->where($qb->expr()->like('evmf.backend', $qb->expr()->literal("%$query%")))
            ->orderBy('title', 'ASC');

        if ($or) {
            $qb->andWhere($or);
        }

        if ($elementTypeIds) {
            $qb->join('e', 'element_version', 'ev', 'e.eid = ev.eid AND ev.element_type_id IN (' . $elementTypeIds . ')');
        }

        $results2 = $conn->fetchAll($qb->getSQL());

        $results = array_merge($results1, $results2);

        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $data = array();
        foreach ($results as $row) {
            $siteroot = $siterootManager->find($row['siteroot_id']);
            $data[] = array(
                'id'    => $row['id'],
                'type'  => ($siterootId === $row['siteroot_id'] ? 'internal' : 'intrasiteroot'),
                'tid'   => $row['id'],
                'eid'   => $row['eid'],
                'title' => $siteroot->getTitle($language)
                    . ' :: ' . $row['title'] . ' [' . $row['id'] . ']',
            );
        }

        return new JsonResponse(array('results' => $data));
    }
}
