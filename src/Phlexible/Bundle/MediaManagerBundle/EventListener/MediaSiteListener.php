<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\EventListener;

use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeManagerInterface;
use Phlexible\Bundle\MediaSiteBundle\Event\CreateFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\FileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\FolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\ReplaceFileEvent;
use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\MediaSiteEvents;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MetaSetBundle\Model\MetaSetManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Media site listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaSiteListener implements EventSubscriberInterface
{
    /**
     * @var DocumenttypeManagerInterface
     */
    private $documenttypeManager;

    /**
     * @var MetaSetManagerInterface
     */
    private $metaSetManager;

    /**
     * @param DocumenttypeManagerInterface $documenttypeManager
     * @param MetaSetManagerInterface      $metaSetManager
     */
    public function __construct(DocumenttypeManagerInterface $documenttypeManager, MetaSetManagerInterface $metaSetManager)
    {
        $this->documenttypeManager = $documenttypeManager;
        $this->metaSetManager = $metaSetManager;
    }


    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            MediaSiteEvents::BEFORE_CREATE_FILE   => array('onBeforeCreateFile', 500),
            MediaSiteEvents::BEFORE_CREATE_FOLDER => array('onBeforeCreateFolder', 500),
            MediaSiteEvents::BEFORE_REPLACE_FILE   => array('onBeforeReplaceFile', 500),
            MediaSiteEvents::BEFORE_DELETE_FILE   => 'onBeforeDeleteFile',
            MediaSiteEvents::BEFORE_DELETE_FOLDER => 'onBeforeDeleteFolder',
        );
    }

    /**
     * @param CreateFileEvent $event
     */
    public function onBeforeCreateFile(CreateFileEvent $event)
    {
        $file = $event->getFile();
        $fileSource = $event->getFileSource();

        $this->processFile($file, $fileSource);
    }

    /**
     * @param ReplaceFileEvent $event
     */
    public function onBeforeReplaceFile(ReplaceFileEvent $event)
    {
        $file = $event->getFile();
        $fileSource = $event->getFileSource();

        $this->processFile($file, $fileSource);
    }

    /**
     * @param FileInterface       $file
     * @param PathSourceInterface $fileSource
     */
    private function processFile(FileInterface $file, PathSourceInterface $fileSource)
    {
        try {
            $documenttype = $this->documenttypeManager->findByMimetype($fileSource->getMimeType());
        } catch (\Exception $e) {
            $documenttype = $this->documenttypeManager->find('binary');
        }

        $file->setAssettype($documenttype->getType());
        $file->setDocumenttype($documenttype->getKey());

        try {
            $fileMetaSet = $this->metaSetManager->findOneByName('file');
            if ($fileMetaSet) {
                $metasets = $file->getAttributes()->get('metasets', array());
                if (!in_array($fileMetaSet->getId(), $metasets)) {
                    $metasets[] = $fileMetaSet->getId();
                    $file->getAttributes()->set('metasets', $metasets);
                }
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @param FolderEvent $event
     */
    public function onBeforeCreateFolder(FolderEvent $event)
    {
        $attributes = $event->getFolder()->getAttributes();

        try {
            $folderMetaSet = $this->metaSetManager->findOneByName('folder');
            if ($folderMetaSet) {
                $metasets = $attributes->get('metasets', array());
                if (!in_array($folderMetaSet->getId(), $metasets)) {
                    $metasets[] = $folderMetaSet->getId();
                    $attributes->set('metasets', $metasets);
                }
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @param FileEvent $event
     */
    public function onBeforeDeleteFile(FileEvent $event)
    {

    }

    /**
     * @param FolderEvent $event
     */
    public function onBeforeDeleteFolder(FolderEvent $event)
    {

    }
}
