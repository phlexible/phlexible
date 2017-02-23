<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ProblemBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Flush command.
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
            ->setName('problem:flush')
            ->setDescription('Flush cached problems.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $problemsRepository = $em->getRepository('PhlexibleProblemBundle:Problem');
        $problems = $problemsRepository->findAll();

        foreach ($problems as $problem) {
            $em->remove($problem);
        }

        $em->flush();

        $output->writeln(count($problems).' problems flushed.');

        return 0;
    }
}
