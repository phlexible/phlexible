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
use Symfony\Component\HttpFoundation\Response;

/**
 * Status controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/status/queue")
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 */
class StatusController extends Controller
{
    /**
     * Return queue status.
     *
     * @return Response
     * @Route("", name="queue_status")
     */
    public function indexAction()
    {
        $jobManager = $this->get('phlexible_queue.job_manager');

        $data = [];

        foreach ($jobManager->findAll() as $job) {
            $data[] = [
                'id' => $job->getId(),
                'command' => $job->getCommand(),
                'arguments' => implode(' ', $job->getArguments()),
                'priority' => $job->getPriority(),
                'status' => $job->getStatus(),
            ];
        }

        $out = '<pre>Current jobs: '.PHP_EOL;
        $out .= print_r($data, 1);

        return new Response($out);
    }
}
