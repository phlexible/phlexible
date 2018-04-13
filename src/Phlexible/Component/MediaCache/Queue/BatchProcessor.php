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

use Phlexible\Component\MediaCache\Worker\InputDescriptor;
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
     * @return \Generator|Instruction[]
     */
    public function process(Batch $batch)
    {
        $flags = $batch->getFlags();

        foreach ($this->inputs($batch->getInputs()) as $input) {
            foreach ($this->templates($batch->getTemplates()) as $template) {
                $instruction = $this->instructionCreator->createInstruction($input, $template, $flags);

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
                if ($template->getManaged()) {
                    yield $template;
                }
            }

            return;
        }

        foreach ($this->templateManager->findAll() as $template) {
            if ($template->getManaged()) {
                yield $template;
            }
        }
    }

    /**
     * @param InputDescriptor[] $files
     *
     * @return \Generator|InputDescriptor
     */
    private function inputs(array $inputs)
    {
        if ($inputs) {
            foreach ($inputs as $input) {
                yield $input;
            }

            return;
        }

        foreach ($this->volumeManager->all() as $volume) {
            $iterator = new RecursiveIteratorIterator($volume->getIterator(), RecursiveIteratorIterator::SELF_FIRST);
            foreach ($iterator as $folder) {
                foreach ($volume->findFilesByFolder($folder) as $file) {
                    $input = InputDescriptor::fromFile($file);
                    yield $input;
                }
            }
        }
    }
}
