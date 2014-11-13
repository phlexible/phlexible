<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Command;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Write command
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
            ->setDescription('Write queued cache files')
            ->addOption('break-on-error', 'b', InputOption::VALUE_NONE, 'Break on error');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $breakOnError = $input->getOption('break-on-error');

        $queueManager = $this->getContainer()->get('phlexible_media_cache.queue_manager');
        $queueWorker = $this->getContainer()->get('phlexible_media_cache.queue.worker');

        $total = $queueManager->countAll();

        if (!$total) {
            return 0;
        }

        $progress = null;
        if ($output->getVerbosity() === OutputInterface::VERBOSITY_NORMAL) {
            $progress = new ProgressBar($output, $total);
            $progress->start();
        }

        $current = 0;
        foreach ($queueManager->findAll() as $queueItem) {
            $current++;
            if ($progress) {
                $progress->advance();
            }

            $cacheItem = $queueWorker->process(
                $queueItem,
                function ($status, $worker, $queueItem, $cacheItem) use ($output, $breakOnError, $current, $total) {
                    $worker = ($worker ? get_class($worker) : '-');
                    $fileId = $queueItem->getFileId();
                    $templateKey = $queueItem->getTemplateKey();
                    if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                        if ($status === 'no_worker' || $status === 'no_cacheitem') {
                            $output->writeln(
                                "$current / $total " .
                                "<fg=red>[$status]</fg=red> " .
                                "worker:$worker " .
                                "fileId:$fileId " .
                                "templateKey:$templateKey"
                            );
                        } elseif ($status === 'processing') {
                            $output->writeln(
                                "$current / $total " .
                                "<info>[$status]</info> " .
                                "worker:$worker " .
                                "fileId:$fileId " .
                                "templateKey:$templateKey"
                            );
                        } else {
                            $mimeType = $cacheItem->getMimeType();
                            $size = $cacheItem->getFileSize();
                            $color = ($status === 'ok' ? 'green' : ($status === 'error' ? 'red' : 'yellow'));
                            $output->writeln(
                                "$current / $total " .
                                "<comment>[result]</comment> " .
                                "<fg=$color>status:$status</fg=$color> " .
                                "worker:$worker " .
                                "fileId:$fileId " .
                                "templateKey:$templateKey " .
                                "mimeType:$mimeType " .
                                "size: $size"
                            );
                        }
                    }
                    if ($status === 'error' && $cacheItem && $cacheItem->getStatus() === CacheItem::STATUS_ERROR) {
                        $output->writeln('<error>' . $cacheItem->getError() . '<error>');
                        if ($breakOnError) {
                            return 1;
                        }
                    }
                }
            );

            $queueManager->deleteQueueItem($queueItem);
        }

        if ($progress) {
            $progress->finish();
            $output->writeln('');
        }

        return 0;
    }
}
