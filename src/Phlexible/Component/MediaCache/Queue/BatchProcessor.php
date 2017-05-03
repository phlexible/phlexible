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

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateManagerInterface;
use Phlexible\Component\Volume\VolumeManager;
use RecursiveIteratorIterator;

/**
 * Batch processor.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BatchProcessor
{
    /**
     * @var InstructionCreator
     */
    private $instructionCreator;

    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @param InstructionCreator       $instructionCreator
     * @param TemplateManagerInterface $templateManager
     * @param VolumeManager            $volumeManager
     */
    public function __construct(
        InstructionCreator $instructionCreator,
        TemplateManagerInterface $templateManager,
        VolumeManager $volumeManager
    ) {
        $this->instructionCreator = $instructionCreator;
        $this->templateManager = $templateManager;
        $this->volumeManager = $volumeManager;
    }

    /**
     * @param Batch $batch
     *
     * @return \Generator|Instruction
     */
    public function process(Batch $batch)
    {
        $flags = $batch->getFlags();

        foreach ($this->files($batch->getFiles()) as $file) {
            foreach ($this->templates($batch->getTemplates()) as $template) {
                $instruction = $this->instructionCreator->createInstruction($file, $template, $flags);

                if (!$instruction) {
                    continue;
                }

                yield $instruction;
            }
        }
    }

    /**
     * @param TemplateInterface[] $templates
     *
     * @return \Generator|TemplateInterface
     */
    private function templates(array $templates)
    {
        if ($templates) {
            foreach ($templates as $template) {
                yield $template;
            }

            return;
        }

        foreach ($this->templateManager->findAll() as $template) {
            yield $template;
        }
    }

    /**
     * @param ExtendedFileInterface[] $files
     *
     * @return \Generator|ExtendedFileInterface
     */
    private function files(array $files)
    {
        if ($files) {
            foreach ($files as $file) {
                yield $file;
            }

            return;
        }

        foreach ($this->volumeManager->all() as $volume) {
            $iterator = new RecursiveIteratorIterator($volume->getIterator(), RecursiveIteratorIterator::SELF_FIRST);
            foreach ($iterator as $folder) {
                foreach ($volume->findFilesByFolder($folder) as $file) {
                    yield $file;
                }
            }
        }
    }
}
