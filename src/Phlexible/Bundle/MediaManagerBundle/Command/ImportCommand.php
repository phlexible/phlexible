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

use Phlexible\Bundle\MediaManagerBundle\MediaManagerMessage;
use Phlexible\Component\Volume\FileSource\CreateFileSource;
use Phlexible\Component\Volume\FileSource\FilesystemFileSource;
use Phlexible\Component\Volume\Model\FolderInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Import command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:import')
            ->setDefinition(
                [
                    new InputArgument('username', InputArgument::REQUIRED, 'Import username'),
                    new InputArgument('source', InputArgument::REQUIRED, 'Source file'),
                    new InputArgument('dir', InputArgument::OPTIONAL, 'Target directory'),
                    new InputOption('volume', null, InputOption::VALUE_REQUIRED, 'Target volume'),
                    new InputOption('delete', null, InputOption::VALUE_NONE, 'Delete source file after import'),
                ]
            )
            ->setDescription('Import file');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $source = $input->getArgument('source');
        $delete = $input->getOption('delete');

        $volume = $input->getOption('volume');
        $volumeManager = $this->getContainer()->get('phlexible_media_manager.volume_manager');

        if ($volume) {
            $volume = $volumeManager->get($volume);
        } else {
            $volume = current($volumeManager->all());
        }

        $targetDir = $input->getArgument('dir');
        if ($targetDir) {
            if (substr($targetDir, -1) !== '/') {
                $targetDir .= '/';
            }

            try {
                $targetFolder = $volume->findFolderByPath($targetDir);
            } catch (\Exception $e) {
                $output->writeln('Folder "'.$targetDir.'" not found.');

                return 1;
            }
        } else {
            $targetFolder = $volume->findFolderByPath('/');
        }

        $userId = $this->getContainer()->get('phlexible_user.user_manager')->findByUsername($username)->getId();

        if (is_dir($source)) {
            $this->importDir($source, $targetFolder, $userId);
            $output->writeln("Imported dir $source");
        } else {
            $this->importFile($source, $targetFolder, $userId, $delete);
            $output->writeln("Imported file $source");
        }

        return 0;
    }

    private function importFile($sourceFile, FolderInterface $targetFolder, $userId, $delete = false)
    {
        $file = new File($sourceFile);
        $fileSource = new FilesystemFileSource($file->getFilename(), $file->getMimeType(), $file->getSize());
        $targetFolder->getVolume()->createFile($targetFolder, $fileSource, [], $userId);

        if ($delete) {
            $filesystem = new Filesystem();
            $filesystem->remove($sourceFile);
        }
    }

    private function importDir($sourceDir, FolderInterface $targetFolder, $userId)
    {
        $baseDir = new \DirectoryIterator($sourceDir);

        foreach ($baseDir as $file) {
            if ($file->isDot()) {
                continue;
            } 
            if (!is_readable($file->getPathName())) {
                continue;
            }
            if ($file->isDir()) {
                $dirName = (string) $file->getFileName();
                $pathName = (string) $file->getPathName();

                $newFolder = $targetFolder->getVolume()->createFolder($targetFolder, $dirName, [], $userId);

                $this->importDir($pathName, $newFolder, $userId);
            } elseif ($file->isFile()) {
                $fileSource = new FilesystemFileSource($file->getFilename(), $file->getMimeType(), $file->getSize());
                $targetFolder->getVolume()->createFile($targetFolder, $fileSource, [], $userId);
            }
        }
    }
}
