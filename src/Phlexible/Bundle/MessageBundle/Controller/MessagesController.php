<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Controller;

use Phlexible\Bundle\MessageBundle\Criteria\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Messages controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/messages/messages")
 */
class MessagesController extends Controller
{
    /**
     * List messages
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="messages_messages")
     */
    public function listAction(Request $request)
    {
        $limit = $request->get('limit', 25);
        $start = $request->get('start', 0);
        $sort = $request->get('sort', 'createdAt');
        $dir = $request->get('dir', 'DESC');
        $filter = $request->get('filter', null);

        if ($filter) {
            $filter = json_decode($filter, true);
        }

        if (!is_array($filter)) {
            $filter = [];
        }

        $messageManager = $this->get('phlexible_message.message_manager');

        $priorityList = $messageManager->getPriorityNames();
        $typeList = $messageManager->getTypeNames();

        $priorityFilter = [];
        $typeFilter = [];
        $channelFilter = [];
        $roleFilter = [];

        $criteria = new Criteria();
        foreach ($filter as $key => $value) {
            if ($key == 'subject' && !empty($value)) {
                $criteria->addRaw(Criteria::CRITERIUM_SUBJECT_LIKE, $value);
            } elseif ($key == 'text' && !empty($value)) {
                $criteria->addRaw(Criteria::CRITERIUM_BODY_LIKE, $value);
            } elseif (substr($key, 0, 9) == 'priority_') {
                $priorityFilter[] = substr($key, 9);
            } elseif (substr($key, 0, 5) == 'type_') {
                $typeFilter[] = substr($key, 5);
            } elseif (substr($key, 0, 8) == 'channel_') {
                $channelFilter[] = substr($key, 8);
            } elseif (substr($key, 0, 5) == 'role_') {
                $roleFilter[] = substr($key, 9);
            } elseif ($key == 'date_after' && !empty($value)) {
                $criteria->addRaw(Criteria::CRITERIUM_START_DATE, $value);
            } elseif ($key == 'date_before' && !empty($value)) {
                $criteria->addRaw(Criteria::CRITERIUM_END_DATE, $value);
            }
        }

        if (count($priorityFilter)) {
            $criteria->addRaw(
                Criteria::CRITERIUM_PRIORITY_IN,
                implode(',', $priorityFilter)
            );
        }

        if (count($typeFilter)) {
            $criteria->addRaw(
                Criteria::CRITERIUM_TYPE_IN,
                implode(',', $typeFilter)
            );
        }

        if (count($channelFilter)) {
            $criteria->addRaw(
                Criteria::CRITERIUM_CHANNEL_IN,
                implode(',', $channelFilter)
            );
        }

        if (count($roleFilter)) {
            $criteria->addRaw(
                Criteria::CRITERIUM_ROLE_IN,
                implode(',', $roleFilter)
            );
        }

        $count = $messageManager->countByCriteria($criteria);
        $messages = $messageManager->findByCriteria($criteria, [$sort => $dir], $limit, $start);

        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                'subject'   => $message->getSubject(),
                'body'      => nl2br($message->getBody()),
                'priority'  => $priorityList[$message->getPriority()],
                'type'      => $typeList[$message->getType()],
                'channel'   => $message->getChannel(),
                'role'      => $message->getRole(),
                'user'      => $message->getUser(),
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse([
            'totalCount' => $count,
            'messages'   => $data,
            'facets'     => $messageManager->getFacetsByCriteria($criteria),
        ]);
    }

    /**
     * List filter values
     *
     * @return JsonResponse
     * @Route("/facets", name="messages_messages_facets")
     */
    public function facetsAction()
    {
        $messageManager = $this->get('phlexible_message.message_manager');

        $filterSets = $messageManager->getFacets();
        $priorityList = $messageManager->getPriorityNames();
        $typeList = $messageManager->getTypeNames();

        $priorities = [];
        arsort($filterSets['priorities']);
        foreach ($filterSets['priorities'] as $priority) {
            $priorities[] = ['id' => $priority, 'title' => $priorityList[$priority]];
        }

        $types = [];
        arsort($filterSets['types']);
        foreach ($filterSets['types'] as $key => $type) {
            $types[] = ['id' => $type, 'title' => $typeList[$type]];
        }

        $channels = [];
        sort($filterSets['channels']);
        foreach ($filterSets['channels'] as $channel) {
            $channels[] = ['id' => $channel, 'title' => $channel ? : '(no channel)'];
        }

        $roles = [];
        sort($filterSets['roles']);
        foreach ($filterSets['roles'] as $role) {
            $roles[] = ['id' => $role, 'title' => $role ? : '(no role)'];
        }

        return new JsonResponse([
            'priorities' => $priorities,
            'types'      => $types,
            'channels'   => $channels,
            'roles'      => $roles,
        ]);
    }
}
