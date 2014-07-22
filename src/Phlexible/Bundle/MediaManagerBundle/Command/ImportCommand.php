<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Command;

use Phlexible\Bundle\MediaManagerBundle\MediaManagerMessage;
use Phlexible\Bundle\MediaSiteBundle\Folder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Import command
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
                array(
                    new InputArgument('source', InputArgument::REQUIRED, 'Source file'),
                    new InputArgument('targetDir', InputArgument::OPTIONAL, 'Target directory'),
                    new InputOption('targetSite', null, InputOption::VALUE_REQUIRED, 'Target site'),
                    new InputOption('delete', null, InputOption::VALUE_NONE, 'Delete source file after import'),
                )
            )
            ->setDescription('Import file');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $delete = $input->getArgument('delete');

        $site = $input->getOption('targetSite');
        $siteManager = $this->getContainer()->get('phlexible_media_site.site_manager');

        if ($site) {
            $site = $siteManager->get($site);
        } else {
            $site = $siteManager->get('mediamanager');
        }

        $targetDir = $input->getArgument('targetDir');
        if ($targetDir) {
            if (substr($targetDir, -1) != '/') {
                $targetDir .= '/';
            }

            try {
                $targetFolder = $site->getFolderPeer()->getByPath($targetDir);
            } catch (\Exception $e) {
                $output->writeln('Folder "' . $targetDir . '" not found.');

                return 1;
            }
        } else {
            $targetFolder = $site->getFolderPeer()->getRoot();
        }

        MWF_Env::setUser(MWF_Core_Users_User_Peer::getSystemUser());

        if (is_dir($source)) {
            $output = $this->importDir($source, $targetFolder);
        } else {
            $output = $this->importFile($source, $targetFolder, $delete);
        }

        $output->writeln($output);

        return 0;
    }

    protected function importFile($sourceFile, Folder $targetFolder, $delete = false)
    {
        try {
            $fileName = basename($sourceFile);

            $targetFolder->importFile($sourceFile, $fileName);

            $output = $sourceFile . ' imported';

            if ($delete) {
                if (unlink($sourceFile)) {
                    $output .= ' and removed';
                } else {
                    $output .= ', but removing failed';
                }
            }
        } catch (\Exception $e) {
            $output = 'Could not import file:' . PHP_EOL . $e->getMessage() . PHP_EOL . $e->getTraceAsString();
        }

        return $output;
    }

    protected function importDir($sourceDir, Folder $targetFolder)
    {
        try {
            $baseDir = new \DirectoryIterator($sourceDir);

            foreach ($baseDir as $file) {
                if ($file->isDot()) {
                    continue;
                } elseif (!is_readable($file->getPathName())) {
                    continue;
                } elseif ($file->isDir()) {
                    $dirName = (string) $file->getFileName();
                    $pathName = (string) $file->getPathName();

                    $newFolder = $targetFolder->createSubFolder($dirName);

                    $this->importDir($pathName, $newFolder);
                } elseif ($file->isFile()) {
                    $sourceFile = (string) $file->getPathName();
                    $fileName = (string) $file->getFileName();

                    $targetFolder->importFile($sourceFile, $fileName);

                    $message = new MediaManagerMessage('File "' . basename($sourceFile) . '" imported.');
                    $message->post();
                }
            }

            $output = $sourceDir . ' imported';
        } catch (\Exception $e) {
            $output = 'Could not import directory:' . PHP_EOL . $e->getMessage() . PHP_EOL . $e->getTraceAsString();
        }

        return $output;
    }

}
