<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Command;

use Phlexible\Bundle\MediaCacheBundle\CacheItem;
use Phlexible\Bundle\MediaCacheBundle\QueueItem;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
            ->addOption('break-on-error', 'b', InputOption::VALUE_NONE, 'Break on error')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $breakOnError = $input->getOption('break-on-error');

        $worker = $this->getContainer()->get('mediacache.queue.worker');

        $worker->processAll(function($status, $worker, $queueItem, $cacheItem) use ($output, $breakOnError) {
            $worker = ($worker ? get_class($worker) : '-');
            $fileId = $queueItem->getFileId();
            $templateKey = $queueItem->getTemplateKey();
            if ($output->getVerbosity() !== OutputInterface::VERBOSITY_QUIET) {
                if ($status === 'no_worker' || $status === 'no_cacheitem') {
                    $output->writeln(
                        "<fg=red>[$status]</fg=red> " .
                        "worker:$worker " .
                        "fileId:$fileId " .
                        "templateKey:$templateKey"
                    );
                } elseif ($status === 'processing') {
                    $output->writeln(
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
                $output->writeln('<error>'.$cacheItem->getError().'<error>');
                if ($breakOnError) {
                    exit(1);
                }
            }
        });

        return 0;
    }
}
