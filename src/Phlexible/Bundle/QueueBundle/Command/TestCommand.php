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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('queue:test')
            ->setDefinition(
                [
                    new InputOption('queue', null, InputOption::VALUE_NONE, 'Queue test job.'),
                    new InputOption('error', null, InputOption::VALUE_NONE, 'Triggers an error.'),
                    new InputOption('exception', null, InputOption::VALUE_NONE, 'Throws an exception.'),
                    new InputOption('exit', null, InputOption::VALUE_NONE, 'Returns exit code 1.'),
                    new InputOption('sleep5', null, InputOption::VALUE_NONE, 'Sleeps for 5 seconds.'),
                    new InputOption('sleep30', null, InputOption::VALUE_NONE, 'Sleeps 6 times for 30 seconds.'),
                    new InputOption('noop', null, InputOption::VALUE_NONE, 'Does nothing.'),
                ]
            )
            ->setDescription('Create test job.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $target = null;
        if ($input->getOption('error')) {
            $target = 'error';
        } elseif ($input->getOption('exception')) {
            $target = 'exception';
        } elseif ($input->getOption('exit')) {
            $target = 'exit';
        } elseif ($input->getOption('sleep5')) {
            $target = 'sleep5';
        } elseif ($input->getOption('sleep30')) {
            $target = 'sleep30';
        } elseif ($input->getOption('noop')) {
            $target = 'noop';
        } else {
            $output->writeln(
                'Your have to pass one of --error, --execption, --die, --exit, --sleep5 or --sleep30'
            );

            return 1;
        }

        if ($input->getOption('queue')) {
            $queueManager = $this->getContainer()->get('phlexible_queue.job_manager');
            $job = new Job('queue:test', ["--$target"]);
            $queueManager->addJob($job);

            $output->writeln("Job created.");
        } else {
            switch ($target) {
                case 'error':
                    $output->writeln('Trigger error.');
                    trigger_error('Something went wrong', E_USER_ERROR);
                    break;

                case 'exception':
                    $output->writeln('Throw exception.');
                    throw new \Exception('Test exception', 2);
                    break;

                case 'exit':
                    $output->writeln('Return with exit code 1');

                    return 1;

                case 'sleep5':
                    $output->writeln('Sleep(5)');
                    sleep(5);
                    $output->writeln('Awake');
                    break;

                case 'sleep30':
                    for ($i = 0; $i < 30; $i = $i + 5) {
                        $output->writeln('Sleep(5) - ' . $i);
                        sleep(5);
                        $output->writeln('Awake');
                    }
                    break;
            }
        }

        return 0;
    }

}
