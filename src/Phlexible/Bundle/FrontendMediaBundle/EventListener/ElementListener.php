<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\EventListener;

use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\Event\ElementVersionEvent;
use Phlexible\Bundle\ElementBundle\Event\SaveElementEvent;
use Phlexible\Bundle\FrontendMediaBundle\Usage\UsageUpdater;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Element listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementListener implements EventSubscriberInterface
{
    /**
     * @var UsageUpdater
     */
    private $usageUpdater;

    /**
     * @param UsageUpdater $usageUpdater
     */
    public function __construct(UsageUpdater $usageUpdater)
    {
        $this->usageUpdater = $usageUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ElementEvents::CREATE_ELEMENT_VERSION => 'onCreateElementVersion',
            ElementEvents::UPDATE_ELEMENT_VERSION => 'onUpdateElementVersion',
            //ElementEvents::SAVE_ELEMENT => 'onSaveElement',
        ];
    }

    /**
     * @param ElementVersionEvent $event
     */
    public function onCreateElementVersion(ElementVersionEvent $event)
    {
        $this->usageUpdater->updateUsage($event->getElementVersion()->getElement());
    }

    /**
     * @param ElementVersionEvent $event
     */
    public function onUpdateElementVersion(ElementVersionEvent $event)
    {
        $this->usageUpdater->updateUsage($event->getElementVersion()->getElement());
    }

    /**
     * @param SaveElementEvent $event
     */
    public function onSaveElement(SaveElementEvent $event)
    {
        if (!$container->components->has('distributionlists')) {
            return;
        }

        $folderRepository = $container->get('frontendmediamanagerChangeFolderRepository');

        $elementVersion = $event->getElementVersion();
        $eid = $elementVersion->getEid();
        $elementData = $elementVersion->getData($event->getLanguage());
        $documentlists = $elementData->getWrap()->all('documentlist');

        // delete old items
        $folderRepository->deleteByEid($eid);

        foreach ($documentlists as $documentlist) {
            $listId = (int) $documentlist->first('documentlist_distribution', true);

            if (!strlen($listId)) {
                continue;
            }

            $folderId = $documentlist->first('documentlist_folder', true);
            if (strlen($folderId)) {
                $folder = $folderRepository->create();
                $folder->listId = $listId;
                $folder->folderId = $folderId;
                $folder->eid = (int) $eid;
                $folderRepository->save($folder);
            }
        }
    }
}
