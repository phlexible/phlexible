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
    private $immediatelyCacheSystemTemplates;

    /**
     * @param TemplateManagerInterface $templateManager
     * @param Worker                   $queueWorker
     * @param BatchResolver            $batchResolver
     * @param bool                     $immediatelyCacheSystemTemplates
     */
    public function __construct(TemplateManagerInterface $templateManager,
                                Worker $queueWorker,
                                BatchResolver $batchResolver,
                                $immediatelyCacheSystemTemplates)
    {
        $this->templateManager = $templateManager;
        $this->queueWorker = $queueWorker;
        $this->batchResolver = $batchResolver;
        $this->immediatelyCacheSystemTemplates = $immediatelyCacheSystemTemplates;
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

        // TODO: queue items
        return;

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
