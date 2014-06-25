<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Phlexible\Bundle\UserBundle\Entity\User;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create command
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
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'Username'),
                new InputArgument('password', InputArgument::REQUIRED, 'Password'),
                new InputArgument('email', InputArgument::REQUIRED, 'Email'),
            ))
            ->setDescription('Create user.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username  = $input->getArgument('username');
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');

        $user = new User();
        $user
            ->setUsername($username)
            ->setPlainPassword($password)
            ->setEmail($email)
            ->setCreatedAt(new \DateTime())
            ->setModifiedAt(new \DateTime())
        ;

        $userRepository = $this->getContainer()->get('phlexible_user.user_manager');
        $userRepository->updateUser($user);

        $output->writeln("Created user $username.");

        return 0;
    }
}
