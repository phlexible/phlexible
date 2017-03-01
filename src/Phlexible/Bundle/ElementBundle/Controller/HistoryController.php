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
 * History controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/history")
 * @Security("is_granted('ROLE_ELEMENTS')")
 */
class HistoryController extends Controller
{
    /**
     * Return Element History.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="elements_history")
     */
    public function indexAction(Request $request)
    {
        $eid = $request->get('filter_eid', null);
        $treeId = $request->get('filter_tree_id', null);
        $teaserId = $request->get('filter_teaser_id', null);
        $action = $request->get('filter_action', null);
        $comment = $request->get('filter_comment', null);
        $sort = $request->get('sort', 'createdAt');
        $dir = $request->get('dir', 'DESC');
        $offset = $request->get('start', 0);
        $limit = $request->get('limit', 25);

        $criteria = [];

        if ($eid) {
            $criteria['eid'] = $eid;
        }
        if ($treeId) {
            $criteria['treeId'] = $treeId;
        }
        if ($teaserId) {
            $criteria['teaserId'] = $teaserId;
        }
        if ($action) {
            $criteria['action'] = $action;
        }
        if ($comment) {
            $criteria['comment'] = $comment;
        }

        $sort = 'id';
        $dir = 'DESC';

        $historyManager = $this->get('phlexible_element.element_history_manager');
        $userManager = $this->get('phlexible_user.user_manager');

        $entries = $historyManager->findBy($criteria, [$sort => $dir], $limit, $offset);
        $count = $historyManager->countBy($criteria);

        $elementHistory = [];
        foreach ($entries as $entry) {
            $type = '-';
            if (stripos($entry->getAction(), 'element')) {
                $type = 'element';
            } elseif (stripos($entry->getAction(), 'node')) {
                $type = 'treeNode';
            } elseif (stripos($entry->getAction(), 'teaser')) {
                $type = 'teaser';
            }

            $username = 'unknown';
            $user = $userManager->find($entry->getCreateUserId());
            if ($user) {
                $username = $user->getUsername();
            }

            $elementHistory[] = [
                'eid' => $entry->getEid(),
                'type' => $type,
                'id' => $entry->getId(),
                'tid' => $entry->getTreeId() ?: $entry->getTeaserId() ?: null,
                'version' => $entry->getVersion(),
                'language' => $entry->getLanguage(),
                'comment' => $entry->getComment(),
                'action' => $entry->getAction(),
                'username' => $username,
                'create_time' => $entry->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        $data = [
            'total' => $count,
            'history' => $elementHistory,
        ];

        return new JsonResponse($data);
    }
}
