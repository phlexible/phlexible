<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Command;

use Brainbits\Mime\MimeDetector;
use Phlexible\Bundle\MediaAssetBundle\Model\AttributeBag;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFolderInterface;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedVolumeInterface;
use Phlexible\Component\Volume\FileSource\FilesystemFileSource;
use Phlexible\Component\Volume\Model\FolderIterator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Read command
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReadCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:read')
            ->addOption('volume', null, InputOption::VALUE_REQUIRED, 'Volume ID')
            ->addOption('folder', null, InputOption::VALUE_REQUIRED, 'Folder ID')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'File ID')
            ->setDescription('Re-read all metadata');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $volumeId = $input->getOption('volume');
        $folderId = $input->getOption('folder');
        $fileId = $input->getOption('file');

        $volumeManager = $this->getContainer()->get('phlexible_media_manager.volume_manager');

        if ($volumeId) {
            $volume = $volumeManager->get($volumeId);

            $this->readVolume($output, $volume);
        } elseif ($folderId) {
            $volume = $volumeManager->getByFolderId($folderId);
            $folder = $volume->findFolder($folderId);

            $this->readVolume($output, $volume, $folder);
        } elseif ($fileId) {
            $volume = $volumeManager->getByFileId($fileId);
            $file = $volume->findFile($fileId);

            $this->readFile($output, $volume, $file);
        } else {
            foreach ($volumeManager->all() as $volume) {
                $this->readVolume($output, $volume);
            }
        }

        return 0;
    }

    private function readVolume(OutputInterface $output, ExtendedVolumeInterface $volume, ExtendedFolderInterface $folder = null)
    {
        $target = $volume;
        if (!$folder) {
            $folder = $volume->findRootFolder();
            $target = $folder;
        }

        $rii = new \RecursiveIteratorIterator(new FolderIterator($target), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $folder) {
            $output->writeln('+ ' . $folder->getName());
            foreach ($volume->findFilesByFolder($folder) as $file) {
                $this->readFile($output, $volume, $file);
            }
        }
    }

    private function readFile(OutputInterface $output, ExtendedVolumeInterface $volume, ExtendedFileInterface $file)
    {
        $output->writeln('  * ' . $file->getId() . ' ' . $file->getPhysicalPath() . ': ');
        $output->write('    > ');

            $mimeDetector = $this->getContainer()->get('phlexible_media_tool.mime.detector');
        $documenttypeManager = $this->getContainer()->get('phlexible_documenttype.documenttype_manager');
        $attributeReader = $this->getContainer()->get('phlexible_media_asset.reader.attribute');

        if (!file_exists($file->getPhysicalPath())) {
            $output->writeln("<error>File not found</error>");

            return;
        }

        $mimetype = $mimeDetector->detect($file->getPhysicalPath(), MimeDetector::RETURN_STRING);
        $attributes = array();

        if (!$mimetype) {
            $output->write("<error>No mimetype</error> ");
            $mimetype = 'application/octet-stream';
            $documenttypeKey = 'binary';
            $assettype = 'DOCUMENT';
        } else {
            try {
                $documenttype = $documenttypeManager->findByMimetype($mimetype);

                $documenttypeKey = $documenttype->getKey();
                $assettype = $documenttype->getType();

                $fileSource = new FilesystemFileSource($file->getPhysicalPath(), $mimetype, filesize($file->getPhysicalPath()));
                if ($attributeReader->supports($fileSource, $documenttypeKey, $assettype)) {
                    $attributeBag = new AttributeBag();
                    $attributeReader->read($fileSource, $documenttypeKey, $assettype, $attributeBag);
                    $attributes = $attributeBag->all();
                    $volume->setFileAttributes($file, $attributes, null);
                }

            } catch (\Exception $e) {
                $output->write("<error>{$e->getMessage()}</error> ");
                $documenttypeKey = 'binary';
                $assettype = 'DOCUMENT';
            }
        }

        $volume->setFileAssetType($file, $assettype, null);
        $volume->setFileDocumenttype($file, $documenttypeKey, null);
        //$volume->setFileMimetype($file, $mimetype, null);

        $output->writeln("$mimetype, $assettype, $documenttypeKey, " . json_encode($attributes));
    }
}
