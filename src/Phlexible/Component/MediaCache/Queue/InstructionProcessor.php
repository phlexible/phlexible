<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Queue;

use Phlexible\Component\MediaCache\Domain\CacheItem;
use Phlexible\Component\MediaCache\Worker\WorkerInterface;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Instruction processor.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class InstructionProcessor
{
    /**
     * @var WorkerInterface
     */
    private $worker;

    /**
     * @var MediaTypeManagerInterface
     */
    private $mediaTypeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param WorkerInterface           $worker
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param LoggerInterface           $logger
     */
    public function __construct(
        WorkerInterface $worker,
        MediaTypeManagerInterface $mediaTypeManager,
        LoggerInterface $logger
    ) {
        $this->worker = $worker;
        $this->mediaTypeManager = $mediaTypeManager;
        $this->logger = $logger;
    }

    /**
     * @param Instruction $instruction
     */
    public function processInstruction(Instruction $instruction)
    {
        $template = $instruction->getTemplate();
        $file = $instruction->getFile();
        $cacheItem = $instruction->getCacheItem();

        $mediaType = $this->mediaTypeManager->find($file->getMediaType());

        if ($this->worker->accept($template, $file, $mediaType)) {
            try {
                $this->worker->process($cacheItem, $template, $file, $mediaType);
            } catch (\Exception $e) {
                $this->logger->info("Worker failed for file {$file->getId()} / mimetype {$file->getMimeType()} / template {$template->getKey()}: ".$e->getMessage());

                return;
            }
            if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK || $cacheItem->getCacheStatus() === CacheItem::STATUS_DELEGATE) {
                $this->logger->info("Status {$cacheItem->getQueueStatus()}/{$cacheItem->getCacheStatus()} for file {$file->getId()} / mimetype {$file->getMimeType()} / template {$template->getKey()}");
            } else {
                $this->logger->info("Status {$cacheItem->getQueueStatus()}/{$cacheItem->getCacheStatus()} for file {$file->getId()} / mimetype {$file->getMimeType()} / template {$template->getKey()}");
            }
        } else {
            $this->logger->warning("No worker for file {$file->getId()} / mimetype {$file->getMimeType()} / template {$template->getKey()}");
        }
    }
}
