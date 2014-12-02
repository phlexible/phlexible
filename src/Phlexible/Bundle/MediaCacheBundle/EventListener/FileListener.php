<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\EventListener;

use Phlexible\Bundle\MediaCacheBundle\Model\CacheManagerInterface;
use Phlexible\Bundle\MediaCacheBundle\Queue\Batch;
use Phlexible\Bundle\MediaCacheBundle\Queue\BatchResolver;
use Phlexible\Bundle\MediaCacheBundle\Queue\QueueProcessor;
use Phlexible\Bundle\MediaSiteBundle\Event\FileEvent;
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
     * @var QueueProcessor
     */
    private $queueProcessor;

    /**
     * @var BatchResolver
     */
    private $batchResolver;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var bool
     */
    private $immediatelyCacheSystemTemplates;

    /**
     * @param TemplateManagerInterface $templateManager
     * @param QueueProcessor           $queueProcessor
     * @param BatchResolver            $batchResolver
     * @param CacheManagerInterface    $cacheManager
     * @param bool                     $immediatelyCacheSystemTemplates
     */
    public function __construct(TemplateManagerInterface $templateManager,
                                QueueProcessor $queueProcessor,
                                BatchResolver $batchResolver,
                                CacheManagerInterface $cacheManager,
                                $immediatelyCacheSystemTemplates)
    {
        $this->templateManager = $templateManager;
        $this->queueProcessor = $queueProcessor;
        $this->batchResolver = $batchResolver;
        $this->cacheManager = $cacheManager;
        $this->immediatelyCacheSystemTemplates = $immediatelyCacheSystemTemplates;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            MediaSiteEvents::CREATE_FILE => 'onCreateFile',
            MediaSiteEvents::REPLACE_FILE => 'onReplaceFile',
            MediaSiteEvents::DELETE_FILE => 'onDeleteFile',
        ];
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

        foreach ($this->cacheManager->findBy(['fileId' => $fileId]) as $cacheItem) {
            $this->cacheManager->deleteCacheItem($cacheItem);
        }
    }

    /**
     * @param FileInterface $file
     */
    private function processFile(FileInterface $file)
    {
        $systemTemplates = $this->templateManager->findBy(['system' => true, 'cache' => true]);
        $otherTemplates = $this->templateManager->findBy(['system' => false, 'cache' => true]);
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

            $this->queueProcessor->processQueue($queue);
        }

        $batch
            ->addFile($file)
            ->addTemplates($otherTemplates);

        $queue = $this->batchResolver->resolve($batch);

        foreach ($queue->all() as $cacheItem) {
            $this->cacheManager->updateCacheItem($cacheItem);
        }
    }
}
