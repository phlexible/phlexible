<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Command;

use Phlexible\Bundle\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Delete command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DeleteCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('user:delete')
            ->setDefinition(
                array(
                    new InputArgument('username', InputArgument::REQUIRED, 'Username / User ID to delete'),
                    new InputArgument('successor', null, InputArgument::REQUIRED, 'Username / User ID to set as successor'),
                )
            )
            ->setDescription('Delete user');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $successorUsername = $input->getArgument('successor');

        $user = $this->findUser($username);
        $successorUser = $this->findUser($successorUsername);

        if (!$user) {
            $output->writeln("<error>User $username not found</error>");
            return 1;
        }

        if (!$successorUser) {
            $output->writeln("<error>Successor user $successorUser not found</error>");
            return 1;
        }

        $output->writeln("Using delete user {$user->getId()}");
        $output->writeln("Using successor user {$successorUser->getId()}");

        $userManager = $this->getContainer()->get('phlexible_user.user_manager');
        $userManager->deleteUser($user, $successorUser);

        $output->writeln('User deleted.');

        return 0;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    private function findUser($username)
    {
        $userManager = $this->getContainer()->get('phlexible_user.user_manager');

        $user = $userManager->find($username);

        if (!$user) {
            $user = $userManager->findByUsername($username);
        }

        if (!$user) {
            return null;
        }

        return $user;
    }
}
