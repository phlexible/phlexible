<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle\Change;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaCache\Queue\Batch;
use Phlexible\Component\MediaCache\Queue\BatchBuilder;
use Phlexible\Component\MediaCache\Queue\BatchProcessor;
use Phlexible\Component\MediaCache\Queue\BatchResolver;
use Phlexible\Component\MediaCache\Queue\Instruction;
use Phlexible\Component\MediaCache\Queue\InstructionProcessor;
use Phlexible\Component\MediaCache\Queue\Queue;
use Phlexible\Component\MediaCache\Queue\QueueProcessor;
use Phlexible\Component\MediaCache\Worker\InputDescriptor;
use Phlexible\Component\MediaTemplate\Model\TemplateManagerInterface;
use Phlexible\Component\Volume\VolumeManager;

/**
 * Template changes.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateChanges
{
    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @var InstructionProcessor
     */
    private $instructionProcessor;

    /**
     * @param TemplateManagerInterface $templateManager
     * @param CacheManagerInterface    $cacheManager
     * @param VolumeManager            $volumeManager
     * @param InstructionProcessor     $instructionProcessor
     */
    public function __construct(
        TemplateManagerInterface $templateManager,
        CacheManagerInterface $cacheManager,
        VolumeManager $volumeManager,
        InstructionProcessor $instructionProcessor
    ) {
        $this->templateManager = $templateManager;
        $this->cacheManager = $cacheManager;
        $this->volumeManager = $volumeManager;
        $this->instructionProcessor = $instructionProcessor;
    }

    /**
     * @return Change[]
     */
    public function changes()
    {
        $changes = [];

        foreach ($this->templateManager->findAll() as $template) {
            $cacheItems = $this->cacheManager->findOutdatedTemplates($template);

            foreach ($cacheItems as $cacheItem) {
                $volume = $this->volumeManager->getByFileId($cacheItem->getFileId());
                $file = $volume->findFile($cacheItem->getFileId());
                $template = $this->templateManager->find($cacheItem->getTemplateKey());
                $change = new Change($file, $template, $cacheItem);

                $changes[] = $change;
            }
        }

        return $changes;
    }

    /**
     * @param bool $viaQueue
     */
    public function commit($viaQueue = false)
    {
        $changes = $this->changes();

        foreach ($changes as $change) {
            $instruction = new Instruction(InputDescriptor::fromFile($change->getFile()), $change->getTemplate(), $change->getCacheItem());
            if ($viaQueue) {
                $instruction->getCacheItem()->setQueueStatus(CacheItem::QUEUE_WAITING);
                $this->cacheManager->updateCacheItem($instruction->getCacheItem());
            } else {
                $this->instructionProcessor->processInstruction($instruction);
            }
        }
    }
}
