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

use Phlexible\Component\MediaManager\AttributeReader\AttributeBag;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedFileVersionInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedVolumeInterface;
use Phlexible\Component\Mime\MimeDetector;
use Phlexible\Component\Volume\FileSource\FilesystemFileSource;
use Phlexible\Component\Volume\Model\FolderIterator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Read command.
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

            $fileVersions = $volume->findFileVersions($fileId);

            foreach ($fileVersions as $fileVersion) {
                $this->readFileVersion($output, $volume, $fileVersion);
            }
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
            $output->writeln('+ '.$folder->getName());
            foreach ($volume->findFilesByFolder($folder) as $file) {
                $this->readFile($output, $volume, $file);
            }
        }
    }

    private function readFile(OutputInterface $output, ExtendedVolumeInterface $volume, ExtendedFileInterface $file)
    {
        $output->writeln('  * '.$file->getId().' v'.$file->getVersion().' '.$file->getPhysicalPath().': ');
        $output->write('    > ');

        $mimeDetector = $this->getContainer()->get('phlexible_media_tool.mime.detector');
        $mediaTypeManager = $this->getContainer()->get('phlexible_media_type.media_type_manager');
        $attributeReader = $this->getContainer()->get('phlexible_media_manager.attribute_reader');

        if (!file_exists($file->getPhysicalPath())) {
            $output->writeln('<error>File not found</error>');

            return;
        }

        $mimetype = $mimeDetector->detect($file->getPhysicalPath(), MimeDetector::RETURN_STRING);

        if (!$mimetype) {
            $mimetype = (new File($file->getPhysicalPath()))->getMimeType();
        }

        if (!$mimetype) {
            $output->write('<error>No mimetype</error> ');
            $mimetype = 'application/octet-stream';
            $mediaTypeName = 'binary';
        } else {
            $mediaType = $mediaTypeManager->findByMimetype($mimetype);
            if ($mediaType) {
                $mediaTypeName = $mediaType->getName();

                $fileSource = new FilesystemFileSource($file->getPhysicalPath(), $mimetype, filesize($file->getPhysicalPath()));
                if ($attributeReader->supports($fileSource, $mediaType)) {
                    $attributeBag = new AttributeBag($file->getAttributes());
                    $attributeReader->read($fileSource, $mediaType, $attributeBag);
                    $attributes = $file->getAttributes();
                    $attributes['fileattributes'] = $attributeBag->all();
                    $volume->setFileAttributes($file, $attributes, null);
                }
            } else {
                $output->write('<error>No media type found</error> ');
                $mediaTypeName = 'binary';
            }
        }

        $volume->setFileMediaType($file, $mediaTypeName, null);
        //$volume->setFileMimetype($file, $mimetype, null);

        $output->writeln("$mimetype, $mediaTypeName, ".(isset($attributes) ? json_encode($attributes) : "No attributes"));
    }

    private function readFileVersion(OutputInterface $output, ExtendedVolumeInterface $volume, ExtendedFileVersionInterface $fileVersion)
    {
        $output->writeln('  * '.$fileVersion->getFileId().' v'.$fileVersion->getVersion().' '.$fileVersion->getPhysicalPath().': ');
        $output->write('    > ');

        $mimeDetector = $this->getContainer()->get('phlexible_media_tool.mime.detector');
        $mediaTypeManager = $this->getContainer()->get('phlexible_media_type.media_type_manager');
        $attributeReader = $this->getContainer()->get('phlexible_media_manager.attribute_reader');

        if (!file_exists($fileVersion->getPhysicalPath())) {
            $output->writeln('<error>File not found</error>');

            return;
        }

        $mimetype = $mimeDetector->detect($fileVersion->getPhysicalPath(), MimeDetector::RETURN_STRING);

        if (!$mimetype) {
            $mimetype = (new File($fileVersion->getPhysicalPath()))->getMimeType();
        }

        if (!$mimetype) {
            $output->write('<error>No mimetype</error> ');
            $mimetype = 'application/octet-stream';
            $mediaTypeName = 'binary';
        } else {
            $mediaType = $mediaTypeManager->findByMimetype($mimetype);
            if ($mediaType) {
                $mediaTypeName = $mediaType->getName();

                $fileSource = new FilesystemFileSource($fileVersion->getPhysicalPath(), $mimetype, filesize($fileVersion->getPhysicalPath()));
                if ($attributeReader->supports($fileSource, $mediaType)) {
                    $attributeBag = new AttributeBag($fileVersion->getAttributes());
                    $attributeReader->read($fileSource, $mediaType, $attributeBag);
                    $attributes = $fileVersion->getAttributes();
                    $attributes['fileattributes'] = $attributeBag->all();
                    $volume->setFileVersionAttributes($fileVersion, $attributes, null);
                }
            } else {
                $output->write('<error>No media type found</error> ');
                $mediaTypeName = 'binary';
            }
        }

        $volume->setFileVersionMediaType($fileVersion, $mediaTypeName, null);
        //$volume->setFileMimetype($file, $mimetype, null);

        $output->writeln("$mimetype, $mediaTypeName, ".(isset($attributes) ? json_encode($attributes) : "No attributes"));
    }
}
