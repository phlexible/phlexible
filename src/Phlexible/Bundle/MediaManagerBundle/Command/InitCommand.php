<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Init command
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
