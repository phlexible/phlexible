<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Queue;

use Brainbits_Util_FileLock as FileLock;
use Phlexible\Bundle\GuiBundle\Properties\Properties;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaCacheBundle\Entity\QueueItem;
use Phlexible\Bundle\MediaCacheBundle\Exception\AlreadyRunningException;
use Phlexible\Bundle\MediaCacheBundle\Queue as BaseQueue;
use Phlexible\Bundle\MediaCacheBundle\Worker\WorkerResolver;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteManager;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateManagerInterface;

/**
 * Queue worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Worker
{
    /**
     * @var WorkerResolver
     */
    private $workerResolver;

    /**
     * @var SiteManager
     */
    private $siteManager;

    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var Properties
     */
    private $properties;

    /**
     * @var string
     */
    private $lockDir;

    /**
     * @param WorkerResolver           $workerResolver
     * @param SiteManager              $siteManager
     * @param TemplateManagerInterface $templateManager
     * @param Properties               $properties
     * @param string                   $lockDir
     */
    public function __construct(WorkerResolver $workerResolver,
                                SiteManager $siteManager,
                                TemplateManagerInterface $templateManager,
                                Properties $properties,
                                $lockDir)
    {
        $this->workerResolver = $workerResolver;
        $this->siteManager = $siteManager;
        $this->templateManager = $templateManager;
        $this->properties = $properties;
        $this->lockDir = $lockDir;
    }

    /**
     * @param QueueItem $queueItem
     * @param callable  $callback
     *
     * @return CacheItem
     */
    public function process(QueueItem $queueItem, callable $callback = null)
    {
        $lock = $this->lock();
        $cacheItem = $this->doProcess($queueItem, $callback);
        $lock->unlock();

        return $cacheItem;
    }

    /**
     * @return FileLock
     * @throws AlreadyRunningException
     */
    private function lock()
    {
        $lock = new FileLock('mediacache_lock', $this->lockDir);
        if (!$lock->tryLock()) {
            throw new AlreadyRunningException('Another cache worker process running.');
        }

        return $lock;
    }

    /**
     * @param QueueItem $queueItem
     * @param callable  $callback
     *
     * @return CacheItem
     */
    private function doProcess(QueueItem $queueItem, callable $callback = null)
    {
        $site = $this->siteManager->getSiteById($queueItem->getSiteId());
        $file = $site->findFile($queueItem->getFileId(), $queueItem->getFileVersion());

        $template = $this->templateManager->find($queueItem->getTemplateKey());

        $worker = $this->workerResolver->resolve($template, $file);
        if (!$worker) {
            if ($callback) {
                call_user_func($callback, 'no_worker', null, $queueItem, null);
            }

            return null;
        }

        if ($callback) {
            call_user_func($callback, 'processing', $worker, $queueItem, null);
        }

        $cacheItem = $worker->process($template, $file);

        if ($callback) {
            if (!$cacheItem) {
                call_user_func($callback, 'no_cacheitem', $worker, $queueItem, null);
            } else {
                call_user_func($callback, $this->mapStatus($cacheItem->getStatus()), $worker, $queueItem, $cacheItem);
            }
        }

        $this->properties->set('mediacache', 'last_run', date('Y-m-d H:i:s'));

        return $cacheItem;
    }

    /**
     * @param int $status
     *
     * @return string
     */
    private function mapStatus($status)
    {
        switch ($status) {
            case CacheItem::STATUS_ERROR:
                return 'error';
            case CacheItem::STATUS_DELEGATE:
                return 'delegate';
            case CacheItem::STATUS_MISSING:
                return 'missing';
            case CacheItem::STATUS_WAITING:
                return 'waiting';
            case CacheItem::STATUS_OK:
                return 'ok';
        }

        return 'unknown';
    }
}
