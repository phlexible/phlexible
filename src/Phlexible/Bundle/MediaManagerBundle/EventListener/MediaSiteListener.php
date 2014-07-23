<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\EventListener;

use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeManagerInterface;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeCreateFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeCreateFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeDeleteFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeDeleteFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\MediaSiteEvents;
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
            MediaSiteEvents::BEFORE_DELETE_FILE   => 'onBeforeDeleteFile',
            MediaSiteEvents::BEFORE_DELETE_FOLDER => 'onBeforeDeleteFolder',
        );
    }

    /**
     * @param BeforeCreateFileEvent $event
     */
    public function onBeforeCreateFile(BeforeCreateFileEvent $event)
    {
        $fileSource = $event->getAction()->getFileSource();

        try {
            $documenttype = $this->documenttypeManager->findByMimetype($fileSource->getMimeType());
        } catch (\Exception $e) {
            $documenttype = $this->documenttypeManager->find('binary');
        }

        $attributes = $event->getAction()->getAttributes();

        $attributes->set('documenttype', $documenttype->getKey());
        $attributes->set('assettype', $documenttype->getType());

        try {
            $fileMetaSet = $this->metaSetManager->findOneByName('file');
            if ($fileMetaSet) {
                $metasets = $attributes->get('metasets', array());
                if (!in_array($fileMetaSet->getId(), $metasets)) {
                    $metasets[] = $fileMetaSet->getId();
                    $attributes->set('metasets', $metasets);
                }
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @param BeforeCreateFolderEvent $event
     */
    public function onBeforeCreateFolder(BeforeCreateFolderEvent $event)
    {
        $attributes = $event->getAction()->getAttributes();

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
     * @param BeforeDeleteFileEvent $event
     */
    public function onBeforeDeleteFile(BeforeDeleteFileEvent $event)
    {

    }

    /**
     * @param BeforeDeleteFolderEvent $event
     */
    public function onBeforeDeleteFolder(BeforeDeleteFolderEvent $event)
    {

    }
}
