<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\QueueBundle\EventListener;

use Phlexible\Bundle\QueueBundle\Model\JobManagerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Console listener
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

        if (isset($_ENV['phlexibleJobId'])) {
            $this->jobId = $_ENV['phlexibleJobId'];
        } elseif (isset($_SERVER['phlexibleJobId'])) {
            $this->jobId = $_SERVER['phlexibleJobId'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ConsoleEvents::EXCEPTION => 'onException',
            ConsoleEvents::TERMINATE => 'onTerminate',
        );
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
