<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle\EventListener;

use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaCache\Queue\BatchBuilder;
use Phlexible\Component\MediaCache\Queue\BatchProcessor;
use Phlexible\Component\MediaCache\Queue\InstructionProcessor;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateManagerInterface;
use Phlexible\Component\Volume\Event\FileEvent;
use Phlexible\Component\Volume\VolumeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * File listener.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileListener implements EventSubscriberInterface
{
    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var BatchProcessor
     */
    private $batchProcessor;

    /**
     * @var InstructionProcessor
     */
    private $instructionProcessor;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var bool
     */
    private $immediatelyCacheSystemTemplates;

    /**
     * @param TemplateManagerInterface $templateManager
     * @param BatchProcessor           $batchProcessor
     * @param InstructionProcessor     $instructionProcessor
     * @param CacheManagerInterface    $cacheManager
     * @param bool                     $immediatelyCacheSystemTemplates
     */
    public function __construct(
        TemplateManagerInterface $templateManager,
        BatchProcessor $batchProcessor,
        InstructionProcessor $instructionProcessor,
        CacheManagerInterface $cacheManager,
        $immediatelyCacheSystemTemplates
    ) {
        $this->templateManager = $templateManager;
        $this->batchProcessor = $batchProcessor;
        $this->instructionProcessor = $instructionProcessor;
        $this->cacheManager = $cacheManager;
        $this->immediatelyCacheSystemTemplates = $immediatelyCacheSystemTemplates;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            VolumeEvents::CREATE_FILE => 'onCreateFile',
            VolumeEvents::REPLACE_FILE => 'onReplaceFile',
            VolumeEvents::DELETE_FILE => 'onDeleteFile',
        ];
    }

    /**
     * @param FileEvent $event
     */
    public function onCreateFile(FileEvent $event)
    {
        $this->processFile($event->getFile());
    }

    /**
     * @param FileEvent $event
     */
    public function onReplaceFile(FileEvent $event)
    {
        $this->processFile($event->getFile());
    }

    /**
     * @param FileEvent $event
     */
    public function onDeleteFile(FileEvent $event)
    {
        $fileId = $event->getFile()->getId();

        foreach ($this->cacheManager->findBy(['fileId' => $fileId]) as $cacheItem) {
            $this->cacheManager->deleteCacheItem($cacheItem);
        }
    }

    /**
     * @param ExtendedFileInterface $file
     */
    private function processFile(ExtendedFileInterface $file)
    {
        $systemTemplates = $this->templateManager->findBy(['system' => true, 'cache' => true, 'managed' => true]);
        $otherTemplates = $this->templateManager->findBy(['system' => false, 'cache' => true, 'managed' => true]);
        foreach ($systemTemplates as $index => $systemTemplate) {
            if ($systemTemplate->getType() !== 'image') {
                $otherTemplates[] = $systemTemplate;
                unset($systemTemplates[$index]);
            }
        }

        if ($this->immediatelyCacheSystemTemplates) {
            $batchBuilder = new BatchBuilder();

            $batchBuilder
                ->files([$file])
                ->templates($systemTemplates);

            $batch = $batchBuilder->getBatch();

            foreach ($this->batchProcessor->process($batch) as $instruction) {
                $this->instructionProcessor->processInstruction($instruction);
            }
        }

        $batchBuilder = new BatchBuilder();

        $batchBuilder
            ->files([$file])
            ->templates($otherTemplates);

        $batch = $batchBuilder->getBatch();

        foreach ($this->batchProcessor->process($batch) as $instruction) {
            $this->cacheManager->updateCacheItem($instruction->getCacheItem());
        }
    }
}
