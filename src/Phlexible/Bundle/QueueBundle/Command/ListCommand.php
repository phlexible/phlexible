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
 * List jobs command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('queue:list')
            ->setDefinition(
                [
                    new InputOption('pending', null, InputOption::VALUE_NONE, 'List pending jobs'),
                    new InputOption('running', null, InputOption::VALUE_NONE, 'List running jobs'),
                    new InputOption('finished', null, InputOption::VALUE_NONE, 'List finished jobs'),
                    new InputOption('failed', null, InputOption::VALUE_NONE, 'List failed jobs'),
                    new InputOption('suspended', null, InputOption::VALUE_NONE, 'List suspended jobs'),
                    new InputOption('aborted', null, InputOption::VALUE_NONE, 'List aborted jobs'),
                    new InputOption('limit', null, InputOption::VALUE_REQUIRED, 'Limit', 10),
                    new InputOption('offset', null, InputOption::VALUE_REQUIRED, 'Offset', 0),
                ]
            )
            ->setDescription('List jobs');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobManager = $this->getContainer()->get('phlexible_queue.job_manager');

        if ($input->getOption('pending')) {
            $criteria = ['state' => Job::STATE_PENDING];
        } elseif ($input->getOption('running')) {
            $criteria = ['state' => Job::STATE_RUNNING];
        } elseif ($input->getOption('finished')) {
            $criteria = ['state' => Job::STATE_FINISHED];
        } elseif ($input->getOption('failed')) {
            $criteria = ['statue' => Job::STATE_FAILED];
        } elseif ($input->getOption('suspended')) {
            $criteria = ['statue' => Job::STATE_SUSPENDED];
        } elseif ($input->getOption('aborted')) {
            $criteria = ['statue' => Job::STATE_ABORTED];
        } else {
            $criteria = [];
        }

        $jobs = $jobManager->findBy(
            $criteria,
            ['createdAt' => 'DESC'],
            $input->getOption('limit'),
            $input->getOption('offset')
        );

        if (empty($jobs)) {
            $output->writeln('No queued jobs');

            return 0;
        }

        $table = new Table($output);

        // set header
        $table->setHeaders(['ID', 'Command', 'Priority', 'Status', 'Created At', 'Execute After']);

        foreach ($jobs as $job) {
            $table->addRow(
                [
                    $job->getId(),
                    $job->getCommand(),
                    $job->getPriority(),
                    $job->getState(),
                    $job->getCreatedAt()->format('Y-m-d H:i:s'),
                    $job->getExecuteAfter()->format('Y-m-d H:i:s'),
                ]
            );
        }

        $table->render($output);

        return 0;
    }

}
