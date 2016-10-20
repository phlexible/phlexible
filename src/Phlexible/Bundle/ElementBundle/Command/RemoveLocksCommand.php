<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Remove locks command
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class RemoveLocksCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('element:remove-locks')
            ->setDescription('Delete all locks');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $lockManager = $container->get('phlexible_element.element_lock_manager');
        foreach ($lockManager->findAll() as $lock) {
            $lockManager->deleteLock($lock);
        }

        $output->writeln('All locks deleted.');

        return 0;
    }
}
