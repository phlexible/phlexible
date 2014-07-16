<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\EventListener;

use Phlexible\Bundle\MediaCacheBundle\Queue\Batch;
use Phlexible\Bundle\MediaCacheBundle\Queue\BatchQueuer;
use Phlexible\Bundle\MediaCacheBundle\Queue\BatchResolver;
use Phlexible\Bundle\MediaCacheBundle\Queue\Queue;
use Phlexible\Bundle\MediaCacheBundle\Queue\Worker;
use Phlexible\Bundle\MediaSiteBundle\Event\CreateFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\DeleteFileEvent;
use Phlexible\Bundle\MediaSiteBundle\MediaSiteEvents;
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
     * @var Queue
     */
    private $queue;

    /**
     * @var Worker
     */
    private $queueWorker;

    /**
     * @var BatchResolver
     */
    private $batchResolver;

    /**
     * @var bool
     */
    private $immediatelyCacheFile;

    /**
     * @param TemplateManagerInterface $templateManager
     * @param Worker                   $queueWorker
     * @param BatchResolver            $batchResolver
     * @param bool                     $immediatelyCacheFile
     */
    public function __construct(TemplateManagerInterface $templateManager,
                                Worker $queueWorker,
                                BatchResolver $batchResolver,
                                $immediatelyCacheFile)
    {
        $this->templateManager = $templateManager;
        $this->queueWorker = $queueWorker;
        $this->batchResolver = $batchResolver;
        $this->immediatelyCacheFile = $immediatelyCacheFile;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            MediaSiteEvents::CREATE_FILE => 'onAddFile',
            MediaSiteEvents::DELETE_FILE => 'onDeleteFile',
        );
    }

    /**
     * @param CreateFileEvent $event
     */
    public function onAddFile(CreateFileEvent $event)
    {
        $file = $event->getAction()->getFile();

        $systemTemplates = $this->templateManager->findBy(array('system' => 1));
        $otherTemplates = $this->templateManager->findBy(array('system' => 0));
        foreach ($systemTemplates as $index => $systemTemplate) {
            if ($systemTemplate->getType() !== 'image') {
                $otherTemplates[] = $systemTemplate;
                unset($systemTemplates[$index]);
            }
        }

        $batch = new Batch();

        if ($this->immediatelyCacheFile) {
            $batch
                ->addFile($file)
                ->addTemplates($systemTemplates);

            $this->batchResolver->resolve($batch);

            return;
        }

        $systemImageTemplates = array();
        $queueTemplates = array();

        foreach ($templates as $template) {
            if ($template->getType() === 'image' && substr($template->getKey(), 0, 4) === '_mm_') {
                $systemImageTemplates[] = $template;
            } else {
                $queueTemplates[] = $template;
            }
        }

        foreach ($systemImageTemplates as $systemImageTemplate) {
            try {
                $queueItem = $this->queue->add($systemImageTemplate, $file);

                $this->queueWorker->process($queueItem);
            } catch (\Exception $e) {
                $queueTemplates[] = $systemImageTemplate;
            }
        }

        $batch
            ->file($file)
            ->templates($queueTemplates);

        $this->batchProcessor->add($batch);
    }

    /**
     * @param DeleteFileEvent $event
     */
    public function onDeleteFile(DeleteFileEvent $event)
    {
        $fileId        = $event->getFile()->getID();
        $fileName      = $event->getFile()->getName();
        $site          = $event->getSite();
        $storageDriver = $site->getStorageDriver();

        $storageDriver->deleteByFileId($fileId, $fileName);
    }
}
