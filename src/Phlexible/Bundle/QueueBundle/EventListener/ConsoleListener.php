<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\QueueBundle\EventListener;

use Phlexible\Bundle\QueueBundle\Model\JobManagerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Console listener.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ConsoleListener implements EventSubscriberInterface
{
    /**
     * @var JobManagerInterface
     */
    private $jobManager;

    /**
     * @var int
     */
    private $jobId;

    /**
     * @param JobManagerInterface $jobManager
     */
    public function __construct(JobManagerInterface $jobManager)
    {
        $this->jobManager = $jobManager;

        $this->jobId = getenv('phlexibleJobId');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::EXCEPTION => 'onException',
            ConsoleEvents::TERMINATE => 'onTerminate',
        ];
    }

    /**
     * @param ConsoleExceptionEvent $event
     */
    public function onException(ConsoleExceptionEvent $event)
    {
        if (!$this->jobId) {
            return;
        }

        $job = $this->jobManager->find($this->jobId);
        $job->setStackTrace(FlattenException::create($event->getException()));

        $this->jobManager->updateJob($job);
    }

    /**
     * @param ConsoleTerminateEvent $event
     */
    public function onTerminate(ConsoleTerminateEvent $event)
    {
        if (!$this->jobId) {
            return;
        }

        $job = $this->jobManager->find($this->jobId);
        $job->setMemoryUsage(memory_get_peak_usage());

        $this->jobManager->updateJob($job);
    }
}
