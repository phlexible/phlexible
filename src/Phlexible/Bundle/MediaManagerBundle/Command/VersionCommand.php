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
 * Version command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VersionCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:version')
            ->setDescription('Set active version for file')
            ->addArgument('username', InputArgument::REQUIRED, 'Username for operation')
            ->addArgument('fileId', InputArgument::REQUIRED, 'File ID')
            ->addArgument('fileVersion', InputArgument::OPTIONAL, 'File version, sets latest version if omitted');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $id = $input->getArgument('fileId');
        $version = $input->getArgument('fileVersion');

        $userId = $this->getContainer()->get('phlexible_user.user_manager')->findByUsername($username)->getId();

        $volumeManager = $this->getContainer()->get('phlexible_media_manager.volume_manager');
        $volume = $volumeManager->getByFileId($id);
        $file = $volume->findFile($id);
        if ($version) {
            $fileVersion = $volume->findFileVersion($id, $version);
        } else {
            $fileVersion = $volume->findOneFileVersion($id, ['fileVersion' => 'DESC']);
        }

        $volume->activateFileVersion($file, $fileVersion, $userId);

        $output->writeln("Activated version {$fileVersion->getVersion()} for file {$file->getName()}");

        return 0;
    }
}
