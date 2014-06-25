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
 * History controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/history")
 * @Security("is_granted('elements')")
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
        $data = array();

        $eid      = $request->get('filter_eid', null);
        $tid      = $request->get('filter_tid', null);
        $teaserId = $request->get('filter_teaser_id', null);
        $action   = $request->get('filter_action', null);
        $comment  = $request->get('filter_comment', null);
        $sort     = $request->get('sort', 'create_time');
        $dir      = $request->get('dir', 'DESC');
        $offset   = $request->get('start', 0);
        $limit    = $request->get('limit', 25);

        $filter = array(
            'eid'       => $eid,
            'tid'       => $tid,
            'teaser_id' => $teaserId,
            'action'    => $action,
            'comment'   => $comment,
        );

        $elementHistory = Makeweb_Elements_History::getRange($filter, $offset, $limit, $sort, $dir);
        $elementTotal   = Makeweb_Elements_History::getCount($filter);

        $data = array(
            'total'   => $elementTotal,
            'history' => $elementHistory,
        );

        return new JsonResponse($data);
    }
}
