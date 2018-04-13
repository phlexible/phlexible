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

use Phlexible\Component\MediaCache\Queue\BatchBuilder;
use Phlexible\Component\MediaCache\Worker\InputDescriptor;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\Volume\Model\FileInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CreateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-cache:create')
            ->setDefinition(
                [
                    new InputArgument('template', InputArgument::REQUIRED, 'Template key.'),
                    new InputArgument('file', InputArgument::REQUIRED, 'File ID.'),
                ]
            )
            ->setDescription('Create chache item.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $batchProcessor = $this->getContainer()->get('phlexible_media_cache.batch_processor');
        $instructionProcessor = $this->getContainer()->get('phlexible_media_cache.instruction_processor');
        $properties = $this->getContainer()->get('properties');

        $batchBuilder = new BatchBuilder();

        $template = $this->getTemplate($input->getArgument('template'));
        $file = $this->getFile($input->getArgument('file'));

        if (!$template && !$file) {
            $output->writeln('Neither file nor template found.');

            return 1;
        }

        if ($template) {
            $batchBuilder = $batchBuilder->templates([$template]);
        }
        if ($file) {
            $batchBuilder = $batchBuilder->input(InputDescriptor::fromFile($file));
        }

        $batch = $batchBuilder->getBatch();

        // create immediately

        foreach ($batchProcessor->process($batch) as $instruction) {
            $instructionProcessor->processInstruction($instruction);

            $output->writeln("File {$instruction->getInput()->getFileId()} / Template {$instruction->getTemplate()->getKey()} processed.");
        }

        $properties->set('mediacache', 'last_run', date('Y-m-d H:i:s'));

        return 0;
    }

    /**
     * @param string $templateKey
     *
     * @return TemplateInterface|null
     */
    private function getTemplate($templateKey)
    {
        $templateManager = $this->getContainer()->get('phlexible_media_template.template_manager');

        return $templateManager->getCollection()->get($templateKey);
    }

    /**
     * @param string $fileId
     *
     * @return FileInterface|null
     */
    private function getFile($fileId)
    {
        $volumeManager = $this->getContainer()->get('phlexible_media_manager.volume_manager');
        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);

        return $file;
    }
}
