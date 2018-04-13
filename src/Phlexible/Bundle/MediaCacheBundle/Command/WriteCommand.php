<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle\Command;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaCache\Queue\Instruction;
use Phlexible\Component\MediaCache\Worker\InputDescriptor;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\Volume\Model\FileInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

/**
 * Write command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class WriteCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-cache:write')
            ->setDescription('Write queued cache files');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileLock = new LockHandler('media-cache-write');
        if (!$fileLock->lock(false)) {
            $output->writeln('<error>Another write process is running</error>');

            return 1;
        }

        $cacheManager = $this->getContainer()->get('phlexible_media_cache.cache_manager');
        $instructionProcessor = $this->getContainer()->get('phlexible_media_cache.instruction_processor');

        while ($cacheItem = $cacheManager->findOneBy(array('queueStatus' => CacheItem::QUEUE_WAITING))) {
            $instruction = new Instruction(
                $this->getInput($cacheItem->getFileId()),
                $this->getTemplate($cacheItem->getTemplateKey()),
                $cacheItem
            );

            $instructionProcessor->processInstruction($instruction);
        }

        $fileLock->release();

        return 0;
    }

    /**
     * @param string $templateKey
     *
     * @return TemplateInterface
     */
    private function getTemplate($templateKey)
    {
        $templateManager = $this->getContainer()->get('phlexible_media_template.template_manager');

        return $templateManager->getCollection()->get($templateKey);
    }

    /**
     * @param string $fileId
     *
     * @return InputDescriptor
     */
    private function getInput($fileId)
    {
        $volumeManager = $this->getContainer()->get('phlexible_media_manager.volume_manager');
        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);

        return InputDescriptor::fromFile($file);
    }
}
