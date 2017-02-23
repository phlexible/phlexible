<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Init command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class InitCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:init')
            ->setDefinition(
                [
                    new InputArgument('name', InputArgument::REQUIRED, 'Volume name'),
                ]
            )
            ->setDescription('Initialise filesystem');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $volumeManager = $this->getContainer()->get('phlexible_media_manager.volume_manager');
        $userManager = $this->getContainer()->get('phlexible_user.user_manager');

        $volume = $volumeManager->get($name);

        $volume->createFolder(null, 'root', array(), $userManager->getSystemUserId());

        return 0;
    }
}
