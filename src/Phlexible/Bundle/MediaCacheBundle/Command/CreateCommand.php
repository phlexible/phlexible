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
use Phlexible\Component\MediaCache\Queue\BatchBuilder;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\Volume\Model\FileInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

/**
 * Queue command.
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
                    new InputOption('all', null, InputOption::VALUE_NONE, 'Create all cachable templates and files.'),
                    new InputOption('template', null, InputOption::VALUE_REQUIRED, 'Create cache items by template key.'),
                    new InputOption('file', null, InputOption::VALUE_REQUIRED, 'Create cache items by File ID.'),
                    new InputOption('not-cached', null, InputOption::VALUE_NONE, 'Only create items that are not yet cached.'),
                    new InputOption('missing', null, InputOption::VALUE_NONE, 'Only create items that are marked as status missing.'),
                    new InputOption('error', null, InputOption::VALUE_NONE, 'Only create items that are marked as status error.'),
                    new InputOption('queue', null, InputOption::VALUE_NONE, 'Use queue instead of immediate creation.'),
                    new InputOption('show', null, InputOption::VALUE_NONE, 'Show matches.'),
                ]
            )
            ->setDescription('Create chache items.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileLock = new LockHandler('media-cache-create');
        if (!$fileLock->lock(false)) {
            $output->writeln('<error>Another create process is running</error>');

            return 1;
        }

        if (!$input->getOption('all')
            && !$input->getOption('template')
            && !$input->getOption('file')
            && !$input->getOption('missing')
            && !$input->getOption('error')
            && !$input->getOption('not-cached')
        ) {
            $output->writeln(
                'Please provide either --all or --template and/or --file and/or --error and/or --missing and/or --error'
            );

            return 1;
        }

        if ($input->getOption('all') && ($input->getOption('template') || $input->getOption('file'))) {
            $output->writeln('Please provide either --all or --template and/or --file');

            return 1;
        }

        $batchProcessor = $this->getContainer()->get('phlexible_media_cache.batch_processor');
        $instructionProcessor = $this->getContainer()->get('phlexible_media_cache.instruction_processor');
        $properties = $this->getContainer()->get('properties');

        $batchBuilder = new BatchBuilder();

        $all = $input->getOption('all');
        if (!$all) {
            $template = $this->getTemplate($input->getOption('template'));
            $file = $this->getFile($input->getOption('file'));

            if ($template) {
                $batchBuilder = $batchBuilder->templates([$template]);
            }
            if ($file) {
                $batchBuilder = $batchBuilder->files([$file]);
            }
        }

        if ($input->getOption('error')) {
            $batchBuilder->filterError();
        }
        if ($input->getOption('not-cached')) {
            $batchBuilder->filterUncached();
        }
        if ($input->getOption('missing')) {
            $batchBuilder->filterMissing();
        }

        $batch = $batchBuilder->getBatch();

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        if ($input->getOption('show')) {
            // only show

            $cnt = 0;
            foreach ($batchProcessor->process($batch) as $instruction) {
                $output->writeln(sprintf(
                    '%-40s %s',
                    $instruction->getTemplate()->getKey(),
                    $instruction->getFile()->getName()
                ));
                $cnt++;
            }

            $output->writeln("$cnt items.");
        } elseif ($input->getOption('queue')) {
            // via queue

            $cacheManager = $this->getContainer()->get('phlexible_media_cache.cache_manager');

            $cnt = 0;
            foreach ($batchProcessor->process($batch) as $instruction) {
                $instruction->getCacheItem()->setQueueStatus(CacheItem::QUEUE_WAITING);
                $cacheManager->updateCacheItem($instruction->getCacheItem());
                $cnt++;
            }

            $output->writeln("$cnt items queued.");
        } else {
            // create immediately

            $cnt = 0;
            foreach ($batchProcessor->process($batch) as $instruction) {
                if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                    $output->writeln('memory usage: '.number_format(memory_get_usage()/1024/1024, 2));
                }
                try {
                    $instructionProcessor->processInstruction($instruction);
                } catch (\Exception $e) {
                    continue;
                }
                $cnt++;
                if ($cnt % 50 === 0) {
                    gc_collect_cycles();
                    $em->clear();
                }
            }

            $output->writeln("$cnt items processed.");
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
        if (!$templateKey) {
            return null;
        }

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
        if (!$fileId) {
            return null;
        }

        $volumeManager = $this->getContainer()->get('phlexible_media_manager.volume_manager');
        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId);

        return $file;
    }
}
