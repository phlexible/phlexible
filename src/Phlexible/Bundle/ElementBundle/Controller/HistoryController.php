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
 * History controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/history")
 * @Security("is_granted('ROLE_ELEMENTS')")
 */
class HistoryController extends Controller
{
    /**
     * Return Element History
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="elements_history")
     */
    public function indexAction(Request $request)
    {
        $eid      = $request->get('filter_eid', null);
        $tid      = $request->get('filter_tid', null);
        $teaserId = $request->get('filter_teaser_id', null);
        $action   = $request->get('filter_action', null);
        $comment  = $request->get('filter_comment', null);
        $sort     = $request->get('sort', 'createdAt');
        $dir      = $request->get('dir', 'DESC');
        $offset   = $request->get('start', 0);
        $limit    = $request->get('limit', 25);

        $criteria = [];

        if ($eid) {
            $criteria['eid'] = $eid;
        }
        if ($tid) {
            $criteria['tid'] = $tid;
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

        if ($sort === 'create_time') {
            $sort = 'createdAt';
        }

        $historyManager = $this->get('phlexible_element.element_history_manager');
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

            $elementHistory[] = [
                'eid'         => $entry->getEid(),
                'type'        => $type,
                'id'          => $entry->getId(),
                'tid'         => $entry->getTreeId() ?: $entry->getTeaserId() ?: null,
                'version'     => $entry->getVersion(),
                'language'    => $entry->getLanguage(),
                'comment'     => $entry->getComment(),
                'action'      => $entry->getAction(),
                'username'    => $entry->getCreateUserId(),
                'create_time' => $entry->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        $data = [
            'total'   => $count,
            'history' => $elementHistory,
        ];

        return new JsonResponse($data);
    }
}
