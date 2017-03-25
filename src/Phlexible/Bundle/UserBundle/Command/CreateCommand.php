<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Command;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CreateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDefinition(
                [
                    new InputArgument('username', InputArgument::REQUIRED, 'Username'),
                    new InputArgument('password', InputArgument::REQUIRED, 'Password'),
                    new InputArgument('email', InputArgument::REQUIRED, 'Email'),
                ]
            )
            ->setDescription('Create user.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');

        $userClass = $this->getContainer()->get('phlexible_user.user.class');
        /* @var UserInterface $user */
        $user = new $userClass;
        $user
            ->setUsername($username)
            ->setPlainPassword($password)
            ->setEmail($email)
            ->setCreatedAt(new \DateTime())
            ->setModifiedAt(new \DateTime());

        $userRepository = $this->getContainer()->get('phlexible_user.user_manager');
        $userRepository->updateUser($user);

        $output->writeln("Created user $username.");

        return 0;
    }
}
