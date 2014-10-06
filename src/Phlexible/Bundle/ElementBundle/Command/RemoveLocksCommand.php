<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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

        $lockManager = $container->get('phlexible_lock.lock_manager');
        foreach ($lockManager->findAll() as $lock) {
            $lockManager->deleteLock($lock);
        }

        $output->writeln('All locks deleted.');

        return 0;
    }
}
