<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\QueueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Data controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/queue")
 * @Security("is_granted('queue')")
 */
class DataController extends Controller
{
    /**
     * Job list
     *
     * @return JsonResponse
     * @Route("/list", name="queue_list")
     */
    public function indexAction()
    {
        $jobManager = $this->get('phlexible_queue.job_manager');

        $data = array();
        foreach ($jobManager->findBy(array(), array('createdAt' => 'DESC')) as $queueItem) {
            $data[] = array(
                'id'          => $queueItem->getId(),
                'command'     => $queueItem->getCommand(),
                'priority'    => $queueItem->getPriority(),
                'status'      => $queueItem->getState(),
                'create_time' => $queueItem->getCreatedAt()->format('Y-m-d H:i:s'),
                'start_time'  => $queueItem->getStartedAt() ? $queueItem->getStartedAt()->format('Y-m-d H:i:s') : null,
                'end_time'    => $queueItem->getFinishedAt() ? $queueItem->getFinishedAt()->format('Y-m-d H:i:s') : null,
                'output'      => nl2br($queueItem->getOutput()),
            );
        }

        return new JsonResponse(array('data' => $data));
    }

}