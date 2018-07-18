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

use Phlexible\Component\MediaCache\Domain\CacheItem;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Stats command.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class StatsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media-cache:stats')
            ->setDescription('Show media cache statistics');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $cacheManager = $container->get('phlexible_media_cache.cache_manager');

        $cntCache = $cacheManager->countAll();
        $cntWaiting = $cacheManager->countBy(['queueStatus' => CacheItem::QUEUE_WAITING]);
        $cntMissing = $cacheManager->countBy(['cacheStatus' => CacheItem::STATUS_MISSING]);
        $cntError = $cacheManager->countBy(['cacheStatus' => CacheItem::STATUS_ERROR]);
        $cntOk = $cacheManager->countBy(['cacheStatus' => CacheItem::STATUS_OK]);
        $cntDelegate = $cacheManager->countBy(['cacheStatus' => CacheItem::STATUS_DELEGATE]);

        $output->writeln($cntCache.' cached items.');
        $output->writeln($cntWaiting.' waiting items.');
        $output->writeln('------------------------------');
        $output->writeln("<info>OK:       $cntOk</info>");
        $output->writeln("Delegate: $cntDelegate");
        $output->writeln("<fg=red>Missing   $cntMissing</fg=red>");
        $output->writeln("<error>Error     $cntError</error>");

        return 0;
    }
}
