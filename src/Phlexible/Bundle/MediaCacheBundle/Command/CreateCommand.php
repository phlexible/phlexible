<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Queue command
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
                array(
                    new InputOption('all', null, InputOption::VALUE_NONE, 'Create all cachable templates and files.'),
                    new InputOption('template', null, InputOption::VALUE_REQUIRED, 'Create cache items by template key.'),
                    new InputOption('file', null, InputOption::VALUE_REQUIRED, 'Create cache items by File ID.'),
                    new InputOption('notCached', null, InputOption::VALUE_NONE, 'Only create items that are not yet cached.'),
                    new InputOption('missing', null, InputOption::VALUE_NONE, 'Only create items that are marked as status missing.'),
                    new InputOption('error', null, InputOption::VALUE_NONE, 'Only create items that are marked as status error.'),
                    new InputOption('queue', null, InputOption::VALUE_NONE, 'Use queue instead of immediate creation.'),
                    new InputOption('show', null, InputOption::VALUE_NONE, 'Show matches.'),
                )
            )
            ->setDescription('Create chache items.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('all')
            && !$input->getOption('template')
            && !$input->getOption('file')
            && !$input->getOption('missing')
            && !$input->getOption('error')
            && !$input->getOption('notCached')
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

        $batchBuilder = $this->getContainer()->get('phlexible_media_cache.queue.batch_builder');

        $all = $input->getOption('all');
        if ($all) {
            $batch = $batchBuilder->createWithAllTemplatesAndFiles();
        } else {
            $templateManager = $this->getContainer()->get('phlexible_media_template.template_manager');
            $siteManager = $this->getContainer()->get('phlexible_media_site.manager');

            $template = $input->getOption('template');
            if ($template) {
                $template = $templateManager->find($template);
            }

            $file = $input->getOption('file');
            if ($file) {
                $file = $siteManager->getByFileId($file)->findFile($file);
            }

            if ($template && $file) {
                $batch = $batchBuilder->createForTemplateAndFile($template, $file);
            } elseif ($template) {
                $batch = $batchBuilder->createWithAllFiles()->addTemplate($template);
            } elseif ($file) {
                $batch = $batchBuilder->createWithAllTemplates()->addFile($file);
            } else {
                $batch = $batchBuilder->create();
            }
        }

        $flags = array();
        if ($input->getOption('error')) {
            $flags[] = 'error';
        }
        if ($input->getOption('notCached')) {
            $flags[] = 'uncached';
        }
        if ($input->getOption('missing')) {
            $flags[] = 'missing';
        }

        $batchResolver = $this->getContainer()->get('phlexible_media_cache.queue.batch_resolver');
        $queue = $batchResolver->resolve($batch, $flags);

        if ($input->getOption('show')) {
            // only show
            $siteManager = $this->getContainer()->get('phlexible_media_site.manager');
            $table = new Table($output);
            $table->setHeaders(array('Idx', 'Template', 'Path', 'File ID'));
            foreach ($queue->all() as $idx => $queueItem) {
                $site = $siteManager->getByFileId($queueItem->getFileId());
                $file = $site->findFile($queueItem->getFileId(), $queueItem->getFileVersion());
                $folder = $site->findFolder($file->getFolderId());
                $table->addRow(
                    array(
                        $idx,
                        $queueItem->getTemplateKey(),
                        $folder->getPath() . $file->getName(),
                        $queueItem->getFileId()
                    )
                );
            }
            $table->render();
            $output->writeln(count($queue) . ' total.');

        } elseif ($input->getOption('queue')) {
            // via queue

            // TODO: add to queue

            $output->writeln(count($queue) . ' items queued.');
        } else {
            // create immediately

            $worker = $this->getContainer()->get('phlexible_media_cache.queue.worker');
            $progress = new ProgressBar($output, count($queue));
            $progress->start();
            foreach ($queue->all() as $queueItem) {
                $worker->process($queueItem);
                $progress->advance();
            }
            $progress->finish();

            $output->writeln('');
            $output->writeln(count($queue) . ' items processed.');
        }


        return 0;
    }
}
