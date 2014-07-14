<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\QueueBundle\Command;

use Phlexible\Bundle\QueueBundle\Entity\Job;
use Phlexible\Bundle\QueueBundle\Model\JobManagerInterface;
use Phlexible\Bundle\QueueBundle\Model\RunningJob;
use Phlexible\Bundle\QueueBundle\QueueMessage;
use Phlexible\Component\Util\FileLock;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Run job(s) command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RunCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var RunningJob[]
     */
    private $runningJobs = array();

    /**
     * @var bool
     */
    private $verbose = false;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('queue:run')
            ->setDefinition(
                array(
                    new InputOption('idle-time', 'i', InputOption::VALUE_REQUIRED, 'Idle time in seconds if no job was found.', 1),
                    new InputOption('max-concurrent-jobs', 'j', InputOption::VALUE_REQUIRED, 'Maximum number of concurrent jobs.', 2),
                    new InputOption('max-runtime', 'r', InputOption::VALUE_REQUIRED, 'Maximum runtime in seconds.', 600),
                )
            )
            ->setDescription('Run queued job(s).');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = new FileLock('queue_lock', $this->getContainer()->getParameter('app.lock_dir'));
        if (!$lock->acquire()) {
            $output->writeln('Another job running.');

            return 1;
        }

        $properties = $this->getContainer()->get('properties');
        $properties->set('queue', 'last_run', date('Y-m-d H:i:s'));

        $idleTime = $input->getOption('idle-time');
        $maxRuntime = $input->getOption('max-runtime');
        $maxJobs = $input->getOption('max-concurrent-jobs');

        $this->output = $output;
        $this->verbose = $output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL;

        $startTime = time();

        while (time() - $startTime < $maxRuntime) {
            $this->checkRunningJobs();

            while (count($this->runningJobs) < $maxJobs) {
                $pendingJob = $this->getJobManager()->findStartableJob();

                if (null === $pendingJob) {
                    sleep($idleTime);
                    continue 2; // Check if the maximum runtime has been exceeded.
                }

                $this->startJob($pendingJob);
            }

            sleep(1);
        }

        if (count($this->runningJobs) > 0) {
            while (count($this->runningJobs) > 0) {
                $this->checkRunningJobs();
                sleep(2);
            }
        }

        $properties->set('queue', 'last_run', date('Y-m-d H:i:s'));

        $lock->release();

        return 0;
    }

    /**
     * @return JobManagerInterface
     */
    private function getJobManager()
    {
        return $this->getContainer()->get('phlexible_queue.job_manager');
    }

    private function checkRunningJobs()
    {
        foreach ($this->runningJobs as $i => $runningJob) {
            $newOutput = substr($runningJob->getProcess()->getOutput(), $runningJob->getOutputPointer());
            $runningJob->setOutputPointer($runningJob->getOutputPointer() + strlen($newOutput));

            $newErrorOutput = substr($runningJob->getProcess()->getErrorOutput(), $runningJob->getErrorOutputPointer());
            $runningJob->setErrorOutputPointer($runningJob->getErrorOutputPointer() + strlen($newErrorOutput));

            if ($this->verbose) {
                if (!empty($newOutput)) {
                    $this->output->writeln(
                        'Job ' . $runningJob->getJob()->getId() . ': ' . str_replace(
                            "\n",
                            "\nJob " . $runningJob->getJob()->getId() . ": ",
                            $newOutput
                        )
                    );
                }

                if (!empty($newErrorOutput)) {
                    $this->output->writeln(
                        'Job ' . $runningJob->getJob()->getId() . ': ' . str_replace(
                            "\n",
                            "\nJob " . $runningJob->getJob()->getId() . ": ",
                            $newErrorOutput
                        )
                    );
                }
            }

            // Check whether this process exceeds the maximum runtime, and terminate if that is
            // the case.
            $runtime = time() - $runningJob->getJob()->getStartedAt()->getTimestamp();
            if ($runningJob->getJob()->getMaxRuntime() > 0 && $runtime > $runningJob->getJob()->getMaxRuntime()) {
                $runningJob->getProcess()->stop(5);

                $this->output->writeln($runningJob->getJob() . ' terminated; maximum runtime exceeded.');
                $runningJob->getJob()->setState(Job::STATE_ABORTED);
                $runningJob->getJob()->setFinishedAt(new \DateTime());
                $this->getJobManager()->updateJob($runningJob->getJob());
                unset($this->runningJobs[$i]);

                $this->createMessage($runningJob->getJob());

                continue;
            }

            if ($runningJob->getProcess()->isRunning()) {
                // For long running processes, it is nice to update the output status regularly.
                $runningJob->getJob()->addOutput($newOutput);
                $runningJob->getJob()->addErrorOutput($newErrorOutput);
                //$runningJob->getJob()->checked();
                $this->getJobManager()->updateJob($runningJob->getJob());

                continue;
            }

            $this->output->writeln(
                $runningJob->getJob() . ' finished with exit code ' . $runningJob->getProcess()->getExitCode() . '.'
            );

            // If the Job exited with an exception, let's reload it so that we
            // get access to the stack trace. This might be useful for listeners.
            $this->getJobManager()->refreshJob($runningJob->getJob());

            $runningJob->getJob()->setExitCode($runningJob->getProcess()->getExitCode());
            $runningJob->getJob()->setOutput($runningJob->getProcess()->getOutput());
            $runningJob->getJob()->setErrorOutput($runningJob->getProcess()->getErrorOutput());
            $runningJob->getJob()->setRuntime(time() - $runningJob->getStartTime());

            $newState = 0 === $runningJob->getProcess()->getExitCode() ? Job::STATE_FINISHED : Job::STATE_FAILED;
            $runningJob->getJob()->setState($newState);
            $runningJob->getJob()->setFinishedAt(new \DateTime());
            $this->getJobManager()->updateJob($runningJob->getJob());

            $this->createMessage($runningJob->getJob());

            unset($this->runningJobs[$i]);
        }
    }

    private function startJob(Job $job)
    {
        $job
            ->setState(Job::STATE_RUNNING)
            ->setStartedAt(new \DateTime());
        $this->getJobManager()->updateJob($job);

        $processBuilder = new ProcessBuilder();

        if (!defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $processBuilder->add('exec');
        }

        $processBuilder
            ->add('php')
            ->add($this->getContainer()->getParameter('kernel.root_dir') . '/console')
            ->setEnv('phlexibleJobId', $job->getId())
            ->add($job->getCommand());

        foreach ($job->getArguments() as $argument) {
            $processBuilder->add($argument);
        }
        $process = $processBuilder->getProcess();
        $process->start();

        $this->output->writeln(sprintf('Started %s.', $job));

        $this->runningJobs[] = new RunningJob($process, $job);
    }

    /**
     * @param Job $job
     */
    private function createMessage(Job $job)
    {
        switch ($job->getState()) {
            case Job::STATE_ABORTED:
                $priority = QueueMessage::PRIORITY_HIGH;
                $type = QueueMessage::TYPE_ERROR;
                $readableStatus = 'aborted';
                break;
            case Job::STATE_SUSPENDED:
                $priority = QueueMessage::PRIORITY_HIGH;
                $type = QueueMessage::TYPE_ERROR;
                $readableStatus = 'suspended';
                break;
            case Job::STATE_FAILED:
                $priority = QueueMessage::PRIORITY_HIGH;
                $type = QueueMessage::TYPE_ERROR;
                $readableStatus = 'failed';
                break;
            default:
                $priority = QueueMessage::PRIORITY_LOW;
                $type = QueueMessage::TYPE_INFO;
                $readableStatus = 'finished';
        }

        $subject = "Job {$job->getId()} $readableStatus.";
        $body = "Runtime: {$job->getRuntime()} s" . PHP_EOL .
            "Command:   {$job->getCommand()}" . PHP_EOL .
            "Exit code: {$job->getExitCode()}" . PHP_EOL .
            "Output:" . PHP_EOL .
            $job->getOutput() . PHP_EOL .
            "Error output:" . PHP_EOL .
            $job->getErrorOutput();

        $message = QueueMessage::create($subject, $body, $priority, $type);
        $this->getContainer()->get('phlexible_message.message_poster')->post($message);
    }
}
