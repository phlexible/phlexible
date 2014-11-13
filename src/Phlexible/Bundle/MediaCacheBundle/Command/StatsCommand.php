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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Stats command
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

        $entityManager = $container->get('doctrine.orm.entity_manager');
        $cacheRepository = $entityManager->getRepository('PhlexibleMediaCacheBundle:CacheItem');
        $queueRepository = $entityManager->getRepository('PhlexibleMediaCacheBundle:QueueItem');

        $cntCache = $cacheRepository->countAll();
        $cntWaiting = $queueRepository->countAll();

        $cntMissing = $cacheRepository->countBy(['status' => CacheItem::STATUS_MISSING]);
        $cntError = $cacheRepository->countBy(['status' => CacheItem::STATUS_ERROR]);
        $cntOk = $cacheRepository->countBy(['status' => CacheItem::STATUS_OK]);
        $cntDelegate = $cacheRepository->countBy(['status' => CacheItem::STATUS_DELEGATE]);

        $output->writeln($cntCache . ' cached items.');
        $output->writeln($cntWaiting . ' waiting items.');
        $output->writeln('------------------------------');
        $output->writeln("<info>OK:       $cntOk</info>");
        $output->writeln("Delegate: $cntDelegate");
        $output->writeln("<fg=red>Missing   $cntMissing</fg=red>");
        $output->writeln("<error>Error     $cntError</error>");

        return 0;
    }

}
