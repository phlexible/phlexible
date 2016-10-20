<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
                [
                    new InputOption('pending', null, InputOption::VALUE_NONE, 'Show statistics for pending jobs'),
                    new InputOption('running', null, InputOption::VALUE_NONE, 'Show statistics for running jobs'),
                    new InputOption('finished', null, InputOption::VALUE_NONE, 'Show statistics for finished jobs'),
                    new InputOption('failed', null, InputOption::VALUE_NONE, 'Show statistics for failed jobs'),
                    new InputOption('aborted', null, InputOption::VALUE_NONE, 'Show statistics for aborted jobs'),
                    new InputOption('suspended', null, InputOption::VALUE_NONE, 'Show statistics for suspended jobs'),
                ]
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

        $jobs = $jobManager->findBy(['state' => $state]);

        $total = count($jobs);

        if ($total) {
            $table = new Table($output);
            $table->setHeaders(['Command', 'Count', 'Percent']);

            $commands = [];
            foreach ($jobs as $job) {
                if (!isset($commands[$job->getCommand()])) {
                    $commands[$job->getCommand()] = 0;
                }
                $commands[$job->getCommand()]++;
            }

            foreach ($commands as $command => $count) {
                $table->addRow(
                    [
                        $command,
                        $count,
                        number_format($count * 100 / $total, 1) . ' %'
                    ]
                );
            }

            if (count($commands) > 1) {
                $table->addRow(
                    [
                        'Total',
                        $total,
                        ''
                    ]
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

        $colors = [
            Job::STATE_PENDING   => 'comment',
            Job::STATE_RUNNING   => null,
            Job::STATE_FINISHED  => 'info',
            Job::STATE_FAILED    => 'error',
            Job::STATE_ABORTED   => null,
            Job::STATE_SUSPENDED => null,
        ];

        $sum = 0;
        foreach ($colors as $state => $color) {
            $sum += $cnt = $jobManager->countBy(['state' => $state]);

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
