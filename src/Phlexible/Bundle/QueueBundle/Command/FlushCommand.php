<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\QueueBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Flush jobs command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FlushCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('queue:flush')
            ->setDefinition(
                [
                    new InputOption('all', null, InputOption::VALUE_NONE, 'Flush all jobs'),
                    new InputOption('pending', null, InputOption::VALUE_NONE, 'Flush pendingjobs'),
                    new InputOption('running', null, InputOption::VALUE_NONE, 'Flush running'),
                    new InputOption('finished', null, InputOption::VALUE_NONE, 'Flush finished jobs'),
                    new InputOption('failed', null, InputOption::VALUE_NONE, 'Flush failed jobs'),
                    new InputOption('suspended', null, InputOption::VALUE_NONE, 'Flush suspended jobs'),
                    new InputOption('aborted', null, InputOption::VALUE_NONE, 'Flush aborted jobs'),
                ]
            )
            ->setDescription('Flush the queue');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobManager = $this->getContainer()->get('phlexible_queue.job_manager');

        if (!$input->getOption('all')
            && !$input->getOption('pending')
            && !$input->getOption('running')
            && !$input->getOption('finished')
            && !$input->getOption('failed')
            && !$input->getOption('suspended')
            && !$input->getOption('aborted')
        ) {
            $output->writeln(
                'Please choose either --all or one or more of the status options: --pending, --running, --finished, --failed, --suspended, --aborted'
            );

            return 1;
        }

        if ($input->getOption('all')
            && ($input->getOption('pending')
                || $input->getOption('running')
                || $input->getOption('finished')
                || $input->getOption('failed')
                || $input->getOption('suspended')
                || $input->getOption('aborted'))
        ) {
            $output->writeln(
                'Please use either --all or one or more of the status options: --pending, --running, --finished, --failed, --suspended, --aborted'
            );

            return 1;
        }

        $states = ['pending', 'running', 'finished', 'failed', 'suspended', 'aborted'];

        if ($input->getOption('all')) {
            $output->writeln('Flushing all jobs.');
            foreach ($states as $state) {
                $jobManager->deleteByState($state);
            }
        } else {
            foreach ($states as $state) {
                if ($input->getOption($state)) {
                    $output->writeln("Flushing $state jobs.");
                    $jobManager->deleteByState($state);
                }
            }
        }

        return 0;
    }
}
