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
