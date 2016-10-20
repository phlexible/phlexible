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
use Phlexible\Component\MediaCache\Queue\Batch;
use Phlexible\Component\MediaCache\Queue\BatchResolver;
use Phlexible\Component\MediaCache\Queue\QueueProcessor;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateManagerInterface;
use Phlexible\Component\Volume\Event\FileEvent;
use Phlexible\Component\Volume\VolumeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * File listener
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
     * @var QueueProcessor
     */
    private $queueProcessor;

    /**
     * @var BatchResolver
     */
    private $batchResolver;

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
     * @param QueueProcessor           $queueProcessor
     * @param BatchResolver            $batchResolver
     * @param CacheManagerInterface    $cacheManager
     * @param bool                     $immediatelyCacheSystemTemplates
     */
    public function __construct(TemplateManagerInterface $templateManager,
                                QueueProcessor $queueProcessor,
                                BatchResolver $batchResolver,
                                CacheManagerInterface $cacheManager,
                                $immediatelyCacheSystemTemplates)
    {
        $this->templateManager = $templateManager;
        $this->queueProcessor = $queueProcessor;
        $this->batchResolver = $batchResolver;
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
        $systemTemplates = $this->templateManager->findBy(['system' => true, 'cache' => true]);
        $otherTemplates = $this->templateManager->findBy(['system' => false, 'cache' => true]);
        foreach ($systemTemplates as $index => $systemTemplate) {
            if ($systemTemplate->getType() !== 'image') {
                $otherTemplates[] = $systemTemplate;
                unset($systemTemplates[$index]);
            }
        }

        $batch = new Batch();

        if ($this->immediatelyCacheSystemTemplates) {
            $batch
                ->addFile($file)
                ->addTemplates($systemTemplates);

            $queue = $this->batchResolver->resolve($batch);

            $this->queueProcessor->processQueue($queue);
        }

        $batch
            ->addFile($file)
            ->addTemplates($otherTemplates);

        $queue = $this->batchResolver->resolve($batch);

        foreach ($queue->all() as $cacheItem) {
            $this->cacheManager->updateCacheItem($cacheItem);
        }
    }
}
