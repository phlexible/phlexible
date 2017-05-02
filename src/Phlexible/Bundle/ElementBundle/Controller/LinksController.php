<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Links controller.
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

        $result = [];

        $links = $linkRepository->findBy(['elementVersion' => $elementVersion, 'language' => $language]);
        if ($incoming) {
            $links = array_merge($links, $linkRepository->findBy(['type' => 'link-internal', 'target' => $node->getId()]));
        }

        foreach ($links as $link) {
            $iconCls = 'p-element-component-icon';
            $content = $link->getTarget();
            switch ($link->getType()) {
                case 'link-internal':
                    $tree = $this->get('phlexible_tree.content_tree_manager')->findByTreeId($link->getTarget());
                    if ($tree) {
                        $node = $tree->get($link->getTarget());
                        if ($node) {
                            $content = sprintf(
                                '%s [%s]',
                                $node->getTitle($language),
                                $link->getTarget()
                            );
                        }
                    }
                    break;
                case 'file':
                    $iconCls = 'p-mediamanager-file-icon';
                    list($fileId, $fileVersion) = explode(';', $link->getTarget());
                    $volume = $this->get('phlexible_media_manager.volume_manager')->findByFileId($fileId);
                    if ($volume) {
                        $file = $volume->findFile($fileId, $fileVersion);
                        if ($file) {
                            $content = $file->getName();
                        }
                    }
                    break;
                case 'folder':
                    $iconCls = 'p-mediamanager-folder-icon';
                    $folderId = $link->getTarget();
                    $volume = $this->get('phlexible_media_manager.volume_manager')->findByFolderId($folderId);
                    if ($volume) {
                        $folder = $volume->findFolder($folderId);
                        if ($folder) {
                            $content = $folder->getName();
                        }
                    }
                    break;
            }
            $result[] = [
                'id' => $link->getId(),
                'iconCls' => $iconCls,
                'language' => $link->getLanguage(),
                'type' => $link->getElementVersion() === $elementVersion ? $link->getType() : 'link-incoming',
                'title' => $link->getField(),
                'content' => $content,
                'link' => [],
                'raw' => $link->getTarget(),
            ];
        }

        return new JsonResponse(['links' => $result]);
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
        }

        $or = null;
        if (!$allowTid || !$allowIntrasiteroot) {
            $or = $qb->expr()->orX();
            if ($allowTid) {
                $or->add($qb->expr()->eq('t.siteroot_id', $qb->expr()->literal($siterootId)));
            }

            if ($allowIntrasiteroot) {
                $or->add($qb->expr()->neq('t.siteroot_id', $qb->expr()->literal($siterootId)));
            }
        }

        $qb
            ->select('t.id', 't.type_id AS eid', 't.siteroot_id', 'evmf.backend AS title')
            ->from('tree', 't')
            ->join('t', 'element', 'e', 't.type_id = e.eid')
            ->join('e', 'element_version', 'ev', 'e.eid = ev.eid AND e.latest_version = ev.version')
            ->join('ev', 'element_version_mapped_field', 'evmf', 'evmf.element_version_id = ev.id AND evmf.language = '.$qb->expr()->literal($language))
            ->where($qb->expr()->eq('t.id', $qb->expr()->literal($query)))
            ->orderBy('title', 'ASC');

        if ($or) {
            $qb->andWhere($or);
        }

        if ($elementTypeIds) {
            $qb->andWhere($qb->expr()->in('e.elementtype_id', $elementTypeIds));
        }

        $results1 = $conn->fetchAll($qb->getSQL());

        $qb = $conn->createQueryBuilder();
        $qb
            ->select('t.id', 't.type_id AS eid', 't.siteroot_id', 'evmf.backend AS title')
            ->from('tree', 't')
            ->join('t', 'element', 'e', 't.type_id = e.eid')
            ->join('e', 'element_version', 'ev', 'e.eid = ev.eid AND e.latest_version = ev.version')
            ->join('ev', 'element_version_mapped_field', 'evmf', 'evmf.element_version_id = ev.id AND evmf.language = '.$qb->expr()->literal($language))
            ->where($qb->expr()->like('evmf.backend', $qb->expr()->literal("%$query%")))
            ->orderBy('title', 'ASC');

        if ($or) {
            $qb->andWhere($or);
        }

        if ($elementTypeIds) {
            $qb->andWhere($qb->expr()->in('e.elementtype_id', $elementTypeIds));
        }

        $results2 = $conn->fetchAll($qb->getSQL());

        $results = array_merge($results1, $results2);

        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $data = [];
        foreach ($results as $row) {
            $siteroot = $siterootManager->find($row['siteroot_id']);
            $data[] = [
                'id' => $row['id'],
                'type' => ($siterootId === $row['siteroot_id'] ? 'internal' : 'intrasiteroot'),
                'tid' => $row['id'],
                'eid' => $row['eid'],
                'title' => $siteroot->getTitle($language)
                    .' :: '.$row['title'].' ['.$row['id'].']',
            ];
        }

        return new JsonResponse(['results' => $data]);
    }
}
