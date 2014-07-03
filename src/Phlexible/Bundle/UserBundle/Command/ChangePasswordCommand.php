<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Change password command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ChangePasswordCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('user:change-password')
            ->setDefinition(
                array(
                    new InputArgument('username', InputArgument::REQUIRED, 'Username'),
                    new InputArgument('password', InputArgument::REQUIRED, 'Password'),
                )
            )
            ->setDescription('Change password for user.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $userRepository = $this->getContainer()->get('phlexible_user.user_manager');

        $user = $userRepository->findByUsername($username);
        $user
            ->setPlainPassword($password);
        $userRepository->updateUser($user);

        $output->writeln("Changed password for user $username.");

        return 0;
    }
}
