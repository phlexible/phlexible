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

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedVolumeInterface;
use Phlexible\Component\Volume\Model\FolderIterator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Meta command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-manager:meta')
            ->setDescription('Apply default metasets');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $volumeManager = $this->getContainer()->get('phlexible_media_manager.volume_manager');
        foreach ($volumeManager->all() as $volume) {
            $rii = new \RecursiveIteratorIterator(new FolderIterator($volume), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($rii as $folder) {
                $output->writeln('+ '.$folder->getName());
                foreach ($volume->findFilesByFolder($folder) as $file) {
                    $output->write('  - '.$file->getName().': ');
                    $this->applyDefaults($output, $volume, $file);
                    $output->writeln('  - '.json_encode($file->getMetaSets()));
                }
            }
        }

        return 0;
    }

    private function applyDefaults(OutputInterface $output, ExtendedVolumeInterface $volume, ExtendedFileInterface $file)
    {
        $metaSetMapper = $this->getContainer()->get('phlexible_media_manager.meta_set_mapper');
        $mediaTypeManager = $this->getContainer()->get('phlexible_media_type.media_type_manager');

        $mediaType = $mediaTypeManager->find($file->getMediaType());

        if (!$mediaType) {
            return;
        }

        $metaSetMapper->map($file, $mediaType);

        $volume->setFileMetasets($file, $file->getMetasets(), null);
    }
}
