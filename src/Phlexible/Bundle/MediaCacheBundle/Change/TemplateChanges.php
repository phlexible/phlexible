<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Change;

use Phlexible\Bundle\MediaCacheBundle\Model\CacheManagerInterface;
use Phlexible\Bundle\MediaCacheBundle\Model\QueueManagerInterface;
use Phlexible\Bundle\MediaCacheBundle\Queue\BatchBuilder;
use Phlexible\Bundle\MediaCacheBundle\Queue\BatchResolver;
use Phlexible\Bundle\MediaCacheBundle\Queue\Queue;
use Phlexible\Bundle\MediaCacheBundle\Queue\Worker;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteManager;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateManagerInterface;

/**
 * Template changes
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateChanges
{
    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var SiteManager
     */
    private $siteManager;

    /**
     * @var BatchBuilder
     */
    private $batchBuilder;

    /**
     * @var BatchResolver
     */
    private $batchResolver;

    /**
     * @var QueueManagerInterface
     */
    private $queueManager;

    /**
     * @var Worker
     */
    private $queueWorker;

    /**
     * @param TemplateManagerInterface $templateManager
     * @param CacheManagerInterface    $cacheManager
     * @param SiteManager              $siteManager
     * @param BatchBuilder             $batchBuilder
     * @param BatchResolver            $batchResolver
     * @param QueueManagerInterface    $queueManager
     * @param Worker                   $queueWorker
     */
    public function __construct(TemplateManagerInterface $templateManager,
                                CacheManagerInterface $cacheManager,
                                SiteManager $siteManager,
                                BatchBuilder $batchBuilder,
                                BatchResolver $batchResolver,
                                QueueManagerInterface $queueManager,
                                Worker $queueWorker)
    {
        $this->templateManager = $templateManager;
        $this->cacheManager = $cacheManager;
        $this->siteManager = $siteManager;
        $this->batchBuilder = $batchBuilder;
        $this->batchResolver = $batchResolver;
        $this->queueManager = $queueManager;
        $this->queueWorker = $queueWorker;
    }

    /**
     * @return Change[]
     */
    public function changes()
    {
        $changes = array();

        foreach ($this->templateManager->findAll() as $template) {
            $cacheItems = $this->cacheManager->findOutdatedTemplates($template);

            foreach ($cacheItems as $cacheItem) {
                $site = $this->siteManager->getByFileId($cacheItem->getFileId());
                $file = $site->findFile($cacheItem->getFileId(), $cacheItem->getFileVersion());
                $template = $this->templateManager->find($cacheItem->getTemplateKey());
                $change = new Change($file, $template, $cacheItem->getTemplateRevision());

                $changes[] = $change;
            }
        }

        return $changes;
    }

    /**
     * @param bool $viaQueue
     */
    public function commit($viaQueue = false)
    {
        $changes = $this->changes();

        $queue = new Queue();
        foreach ($changes as $change) {
            $batch = $this->batchBuilder->createForTemplateAndFile($change->getTemplate(), $change->getFile());
            $changeQueue = $this->batchResolver->resolve($batch);
            $queue->merge($changeQueue);
        }

        foreach ($queue->all() as $queueItem) {
            if ($viaQueue) {
                $this->queueManager->updateQueueItem($queueItem);
            } else {
                $this->queueWorker->process($queueItem);
            }
        }
    }
}
