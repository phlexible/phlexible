<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\QueueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Data controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/queue")
 * @Security("is_granted('ROLE_QUEUE')")
 */
class DataController extends Controller
{
    /**
     * Job list.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/list", name="queue_list")
     */
    public function indexAction(Request $request)
    {
        $jobManager = $this->get('phlexible_queue.job_manager');

        $limit = $request->get('limit', 25);
        $offset = $request->get('start', 0);

        $data = [];
        foreach ($jobManager->findBy([], ['createdAt' => 'DESC'], $limit, $offset) as $queueItem) {
            $data[] = [
                'id' => $queueItem->getId(),
                'command' => $queueItem->getCommand(),
                'priority' => $queueItem->getPriority(),
                'status' => $queueItem->getState(),
                'create_time' => $queueItem->getCreatedAt()->format('Y-m-d H:i:s'),
                'start_time' => $queueItem->getStartedAt() ? $queueItem->getStartedAt()->format('Y-m-d H:i:s') : null,
                'end_time' => $queueItem->getFinishedAt() ? $queueItem->getFinishedAt()->format('Y-m-d H:i:s') : null,
                'output' => nl2br($queueItem->getOutput()),
            ];
        }

        $total = $jobManager->countBy([]);

        return new JsonResponse(['data' => $data, 'total' => $total]);
    }
}
