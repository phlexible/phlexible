<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\EventListener;

use Phlexible\Bundle\MediaCacheBundle\Model\CacheManagerInterface;
use Phlexible\Bundle\MediaCacheBundle\Model\QueueManagerInterface;
use Phlexible\Bundle\MediaCacheBundle\Queue\Batch;
use Phlexible\Bundle\MediaCacheBundle\Queue\BatchResolver;
use Phlexible\Bundle\MediaCacheBundle\Queue\Worker;
use Phlexible\Bundle\MediaSiteBundle\Event\CreateFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\FileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\ReplaceFileEvent;
use Phlexible\Bundle\MediaSiteBundle\MediaSiteEvents;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * File listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileListener implements EventSubscriberInterface
{
    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var Worker
     */
    private $queueWorker;

    /**
     * @var BatchResolver
     */
    private $batchResolver;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var QueueManagerInterface
     */
    private $queueManager;

    /**
     * @var bool
     */
    private $immediatelyCacheSystemTemplates;

    /**
     * @param TemplateManagerInterface $templateManager
     * @param Worker                   $queueWorker
     * @param BatchResolver            $batchResolver
     * @param CacheManagerInterface    $cacheManager
     * @param QueueManagerInterface    $queueManager
     * @param bool                     $immediatelyCacheSystemTemplates
     */
    public function __construct(TemplateManagerInterface $templateManager,
                                Worker $queueWorker,
                                BatchResolver $batchResolver,
                                CacheManagerInterface $cacheManager,
                                QueueManagerInterface $queueManager,
                                $immediatelyCacheSystemTemplates)
    {
        $this->templateManager = $templateManager;
        $this->queueWorker = $queueWorker;
        $this->batchResolver = $batchResolver;
        $this->cacheManager = $cacheManager;
        $this->queueManager = $queueManager;
        $this->immediatelyCacheSystemTemplates = $immediatelyCacheSystemTemplates;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            MediaSiteEvents::CREATE_FILE => 'onCreateFile',
            MediaSiteEvents::REPLACE_FILE => 'onReplaceFile',
            MediaSiteEvents::DELETE_FILE => 'onDeleteFile',
        );
    }

    /**
     * @param FileEvent $event
     */
    public function onCreateFile(FileEvent $event)
    {
        $this->processFile($event->getFile());
    }

    /**
     * @param FileEvent $event
     */
    public function onReplaceFile(FileEvent $event)
    {
        $this->processFile($event->getFile());
    }

    /**
     * @param FileEvent $event
     */
    public function onDeleteFile(FileEvent $event)
    {
        $fileId = $event->getFile()->getId();

        foreach ($this->cacheManager->findBy(array('fileId' => $fileId)) as $cacheItem) {
            $this->cacheManager->deleteCacheItem($cacheItem);
        }

        foreach ($this->queueManager->findBy(array('fileId' => $fileId)) as $queueItem) {
            $this->queueManager->deleteQueueItem($queueItem);
        }
    }

    /**
     * @param FileInterface $file
     */
    private function processFile(FileInterface $file)
    {
        $systemTemplates = $this->templateManager->findBy(array('system' => true, 'cache' => true));
        $otherTemplates = $this->templateManager->findBy(array('system' => false, 'cache' => true));
        foreach ($systemTemplates as $index => $systemTemplate) {
            if ($systemTemplate->getType() !== 'image') {
                $otherTemplates[] = $systemTemplate;
                unset($systemTemplates[$index]);
            }
        }

        $batch = new Batch();

        if ($this->immediatelyCacheSystemTemplates) {
            $batch
                ->addFile($file)
                ->addTemplates($systemTemplates);

            $queue = $this->batchResolver->resolve($batch);

            foreach ($queue->all() as $queueItem) {
                $this->queueWorker->process($queueItem);
            }
        }

        $batch
            ->addFile($file)
            ->addTemplates($otherTemplates);

        $queue = $this->batchResolver->resolve($batch);

        foreach ($queue->all() as $queueItem) {
            $this->queueManager->updateQueueItem($queueItem);
        }
    }
}
