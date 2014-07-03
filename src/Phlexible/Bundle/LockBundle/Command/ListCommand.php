<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\LockBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List locks command
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class ListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('locks:list')
            ->setDescription('List locks');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $userManager = $container->get('phlexible_user.user_manager');
        $lockManager = $container->get('phlexible_lock.lock_manager');
        $locks = $lockManager->findAll();

        if (count($locks)) {
            $table = new Table($output);
            $table->setHeaders(array('User', 'Lock time', 'Object type', 'Object ID', 'Lock type'));

            foreach ($locks as $lock) {
                $user = $userManager->find($lock->getUserId());

                $table->addRow(
                    array(
                        $user->getUsername(),
                        $lock->getLockedAt()->format('Y-m-d H:i:s'),
                        $lock->getObjectType(),
                        $lock->getObjectId(),
                        $lock->getType()
                    )
                );
            }

            $table->render($output);
        } else {
            $output->writeln('No locks.');
        }

        return 0;
    }
}
