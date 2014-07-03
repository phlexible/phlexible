<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\QueueBundle\Command;

use Phlexible\Bundle\QueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Job statistics command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class StatsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('queue:stats')
            ->setDefinition(
                array(
                    new InputOption('pending', null, InputOption::VALUE_NONE, 'Show statistics for pending jobs'),
                    new InputOption('running', null, InputOption::VALUE_NONE, 'Show statistics for running jobs'),
                    new InputOption('finished', null, InputOption::VALUE_NONE, 'Show statistics for finished jobs'),
                    new InputOption('failed', null, InputOption::VALUE_NONE, 'Show statistics for failed jobs'),
                    new InputOption('aborted', null, InputOption::VALUE_NONE, 'Show statistics for aborted jobs'),
                    new InputOption('suspended', null, InputOption::VALUE_NONE, 'Show statistics for suspended jobs'),
                )
            )
            ->setDescription('Show statistics');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('pending')) {
            $this->getJobStatistics($output, Job::STATE_PENDING);
        } elseif ($input->getOption('running')) {
            $this->getJobStatistics($output, Job::STATE_RUNNING);
        } elseif ($input->getOption('finished')) {
            $this->getJobStatistics($output, Job::STATE_FINISHED);
        } elseif ($input->getOption('failed')) {
            $this->getJobStatistics($output, Job::STATE_FAILED);
        } elseif ($input->getOption('suspended')) {
            $this->getJobStatistics($output, Job::STATE_SUSPENDED);
        } elseif ($input->getOption('aborted')) {
            $this->getJobStatistics($output, Job::STATE_ABORTED);
        } else {
            $this->getMainStatistics($output);
        }

        return 0;
    }

    private function getJobStatistics(OutputInterface $output, $state)
    {
        $jobManager = $this->getContainer()->get('phlexible_queue.job_manager');

        $jobs = $jobManager->findBy(array('state' => $state));

        $total = count($jobs);

        if ($total) {
            $table = new Table($output);
            $table->setHeaders(array('Command', 'Count', 'Percent'));

            $commands = array();
            foreach ($jobs as $job) {
                if (!isset($commands[$job->getCommand()])) {
                    $commands[$job->getCommand()] = 0;
                }
                $commands[$job->getCommand()]++;
            }

            foreach ($commands as $command => $count) {
                $table->addRow(
                    array(
                        $command,
                        $count,
                        number_format($count * 100 / $total, 1) . ' %'
                    )
                );
            }

            if (count($commands) > 1) {
                $table->addRow(
                    array(
                        'Total',
                        $total,
                        ''
                    )
                );
            }

            $table->render($output);
        } else {
            $output->writeln('No jobs found.');
        }
    }

    private function getMainStatistics(OutputInterface $output)
    {
        $jobManager = $this->getContainer()->get('phlexible_queue.job_manager');

        $colors = array(
            Job::STATE_PENDING   => 'comment',
            Job::STATE_RUNNING   => null,
            Job::STATE_FINISHED  => 'info',
            Job::STATE_FAILED    => 'error',
            Job::STATE_ABORTED   => null,
            Job::STATE_SUSPENDED => null,
        );

        $sum = 0;
        foreach ($colors as $state => $color) {
            $sum += $cnt = $jobManager->countBy(array('state' => $state));

            if ($cnt) {
                $output->writeln(
                    ($cnt ? ($color ? '<' . $color . '>' : '') . $cnt : 'No') . ' ' . $state . ' jobs.' . ($cnt && $color ? '</' . $color . '>' : '')
                );
            }
        }

        if ($sum) {
            $output->writeln('------------------------------------------');
            $output->writeln('Total ' . $sum . ' job(s) in queue.');
        } else {
            $output->writeln('No jobs in queue.');
        }
    }
}
