<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\EventListener;

/**
 * Media listener
 *
 * @author Michael van Engelshoven <mve@brainbits.net>
 */
class MediaListener
{
    /**
     * callback for file upload
     *
     * @param Media_Site_Event_SaveUpload $upload
     * @param array                       $params
     */
    public function onUploadFile(Media_Site_Event_SaveUpload $event, array $params)
    {
        $container               = $params['container'];

        if (!$container->components->has('distributionslists'))
        {
            return;
        }

        $folderRepository        = $container->get('frontendmediamanagerChangeFolderRepository');
        $changesRepository       = $container->get('distributionlistsChangesRepository');

        // get folder id
        $folderId = $event->getFolder()->getID();
        $file     = $event->getFile();

        $folderLists = $folderRepository->getAllFolderListItems($folderId);
        foreach ($folderLists as $folderList)
        {
            $createDate = new Zend_Date();
            $data = array(
                'fileTitle' => $file->getName(),
                'fileVersion' => '1',
                'fileDate'    => $createDate->toString('yyyy-MM-dd HH:mm:ss'),
                'eid'         => (int) $folderList->eid,
                'action'      => 'create',
            );

            // create change object and save it
            $change           = $changesRepository->create();
            $change->type     = 'folders';
            $change->data     = $data;
            $change->listIds  = array($folderList->listId);
            $changesRepository->save($change);
        }
    }

    /**
     * callback for file changes (save meta information)
     *
     * @param Media_Manager_Event_SaveMeta $event
     * @param array                        $params
     */
    public function onChangeFile(Media_Manager_Event_SaveMeta $event, array $params)
    {
        $container               = $params['container'];

        if (!$container->components->has('distributionslists'))
        {
            return;
        }

        $folderRepository        = $container->get('frontendmediamanagerChangeFolderRepository');
        $changesRepository       = $container->get('distributionlistsChangesRepository');

        $folderId = $event->getFile()->getFolderID();
        $file     = $event->getFile();

        $folderLists = $folderRepository->getAllFolderListItems($folderId);
        foreach ($folderLists as $folderList)
        {
            $modifiyDate = new Zend_Date();
            $data = array(
                'fileTitle' => $file->getName(),
                'fileVersion' => $file->getVersion(),
                'fileDate'    => $modifiyDate->toString('yyyy-MM-dd HH:mm:ss'),
                'eid'         => (int) $folderList->eid,
                'action'      => 'update',
            );

            // create change object and save it
            $change           = $changesRepository->create();
            $change->type     = 'folders';
            $change->data     = $data;
            $change->listIds  = array($folderList->listId);
            $changesRepository->save($change);
        }
    }

    /**
     * callback for deleting a file
     *
     * @param Media_Site_Event_DeleteFile $event
     * @param array                       $params
     */
    public function onDeleteFile(Media_Site_Event_DeleteFile $event, array $params)
    {
        $container               = $params['container'];

        if (!$container->components->has('distributionslists'))
        {
            return;
        }

        $folderRepository        = $container->get('frontendmediamanagerChangeFolderRepository');
        $changesRepository       = $container->get('distributionlistsChangesRepository');

        $folderId = $event->getFolder()->getID();
        $file     = $event->getFile();

        $folderLists = $folderRepository->getAllFolderListItems($folderId);
        foreach ($folderLists as $folderList)
        {
            $modifiyDate = new Zend_Date();
            $data = array(
                'fileTitle' => $file->getName(),
                'fileVersion' => $file->getVersion(),
                'fileDate'    => $modifiyDate->toString('yyyy-MM-dd HH:mm:ss'),
                'eid'         => (int) $folderList->eid,
                'action'      => 'delete',
            );

            // create change object and save it
            $change           = $changesRepository->create();
            $change->type     = 'folders';
            $change->data     = $data;
            $change->listIds  = array($folderList->listId);
            $changesRepository->save($change);
        }
    }
}
