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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Successor command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SuccessorCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('user:successor')
            ->setDefinition(
                [
                    new InputArgument('fromUsername', InputArgument::REQUIRED, 'From username'),
                    new InputArgument('toUsername', null, InputArgument::REQUIRED, 'Successor username'),
                ]
            )
            ->setDescription('Set successor.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fromUsername = $input->getArgument('fromUsername');
        $toUsername = $input->getArgument('toUsername');

        $userRepository = $this->getContainer()->get('phlexible_user.user_manager');

        $fromUser = $userRepository->findByUsername($fromUsername);
        $toUser = $userRepository->findByUsername($toUsername);

        $successorService = $this->getContainer()->get('phlexible_user.successor_service');
        $successorService->set($fromUser, $toUser);

        return 0;
    }
}
