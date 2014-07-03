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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Export command
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
                array(
                    new InputArgument('site', InputArgument::REQUIRED, 'Site to export.'),
                    new InputArgument('target', InputArgument::REQUIRED, 'Target directory.'),
                    new InputOption('symlink', null, InputOption::VALUE_NONE, 'Symlink'),
                )
            )
            ->setDescription('Export site');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $site = $input->getArgument('site');
        $target = $input->getArgument('target');
        $symlink = $input->getOption('symlink');

        if (substr($target, 0, 1) !== '/') {
            $target = realpath($target);
        }
        if (!file_exists($target)) {
            $output->writeln('Target directory has to exist and needs to be empty.');

            return 1;
        }
        $target = rtrim($target, '/') . '/';

        $siteManager = $this->getContainer()->get('phlexible_media_site.manager');
        $site = $siteManager->getSiteById($site);

        $filesystem = new Filesystem();

        $rii = new \RecursiveIteratorIterator($site->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $folder) {
            $folderPath = $folder->getPath();

            $filesystem->mkdir($target . $folderPath);

            foreach ($site->findFilesByFolder($folder) as $file) {
                $fileName = $file->getName();
                $filePath = $file->getPhysicalPath();

                $targetPath = $target . $folderPath . $fileName;

                if ($symlink) {
                    if ($filesystem->exists($filePath)) {
                        $filesystem->symlink($filePath, $targetPath);
                    } else {
                        $filesystem->symlink($filePath . '.nonexistant', $targetPath);
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
