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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Export command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:export')
            ->setDefinition(
                [
                    new InputArgument('volume', InputArgument::REQUIRED, 'Volume to export.'),
                    new InputArgument('target', InputArgument::REQUIRED, 'Target directory.'),
                    new InputOption('symlink', null, InputOption::VALUE_NONE, 'Symlink'),
                ]
            )
            ->setDescription('Export volume');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $volume = $input->getArgument('volume');
        $target = $input->getArgument('target');
        $symlink = $input->getOption('symlink');

        if (substr($target, 0, 1) !== '/') {
            $target = realpath($target);
        }
        if (!file_exists($target)) {
            $output->writeln('Target directory has to exist and needs to be empty.');

            return 1;
        }
        $target = rtrim($target, '/').'/';

        $volumeManager = $this->getContainer()->get('phlexible_media_manager.volume_manager');
        $volume = $volumeManager->getById($volume);

        $filesystem = new Filesystem();

        $rii = new \RecursiveIteratorIterator($volume->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $folder) {
            $folderPath = $folder->getPath();

            $filesystem->mkdir($target.$folderPath);

            foreach ($volume->findFilesByFolder($folder) as $file) {
                $fileName = $file->getName();
                $filePath = $file->getPhysicalPath();

                $targetPath = $target.$folderPath.$fileName;

                if ($symlink) {
                    if ($filesystem->exists($filePath)) {
                        $filesystem->symlink($filePath, $targetPath);
                    } else {
                        $filesystem->symlink($filePath.'.nonexistant', $targetPath);
                    }
                } else {
                    if ($filesystem->exists($filePath)) {
                        $filesystem->copy($filePath, $targetPath);
                    } else {
                        $filesystem->touch($targetPath);
                    }
                }
            }
        }

        $output->writeln('bnla');

        return 0;
    }
}
