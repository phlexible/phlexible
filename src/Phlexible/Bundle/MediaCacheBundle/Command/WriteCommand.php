<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Command;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaCacheBundle\Entity\QueueItem;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
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

        $cacheManager = $this->getContainer()->get('phlexible_media_cache.cache_manager');
        $queueProcessor = $this->getContainer()->get('phlexible_media_cache.queue.processor');

        $total = $cacheManager->countBy(array('queueStatus' => CacheItem::QUEUE_WAITING));

        if (!$total) {
            return 0;
        }

        $progress = null;
        if ($output->getVerbosity() === OutputInterface::VERBOSITY_NORMAL) {
            $progress = new ProgressBar($output, $total);
            $progress->start();
        }

        $current = 0;
        foreach ($cacheManager->findBy(array('queue_status' => CacheItem::QUEUE_WAITING)) as $cacheItem) {
            $current++;
            if ($progress) {
                $progress->advance();
            }

            $queueProcessor->processItem(
                $cacheItem,
                function ($status, $worker, CacheItem $cacheItem) use ($output, $breakOnError, $current, $total) {
                    $worker = ($worker ? get_class($worker) : '-');
                    $fileId = $cacheItem->getFileId();
                    $templateKey = $cacheItem->getTemplateKey();
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
                    if ($status === 'error' && $cacheItem && $cacheItem->getCacheStatus() === CacheItem::STATUS_ERROR) {
                        $output->writeln('<error>' . $cacheItem->getError() . '<error>');
                        if ($breakOnError) {
                            return 1;
                        }
                    }
                }
            );
        }

        if ($progress) {
            $progress->finish();
            $output->writeln('');
        }

        return 0;
    }
}
