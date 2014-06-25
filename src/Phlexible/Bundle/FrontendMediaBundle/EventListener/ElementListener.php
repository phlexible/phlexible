<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\EventListener;

/**
 * Render listener
 *
 * @author Michael van Engelshoven <mve@brainbits.net>
 */
class ElementListener
{
    /**
     * callback to save distributionlist and folder
     *
     * @param Makeweb_Elements_Event_SaveElement $event
     * @param array                              $params
     */
    public function onSaveElement(Makeweb_Elements_Event_SaveElement $event, array $params)
    {
        /* @var $container MWF_Container_ContainerInterface */
        $container = $params['container'];

        if (!$container->components->has('distributionlists'))
        {
            return;
        }

        $folderRepository = $container->get('frontendmediamanagerChangeFolderRepository');

        $elementVersion = $event->getElementVersion();
        $eid            = $elementVersion->getEid();
        $elementData    = $elementVersion->getData($event->getLanguage());
        $documentlists  = $elementData->getWrap()->all('documentlist');

        // delete old items
        $folderRepository->deleteByEid($eid);

        foreach ($documentlists as $documentlist)
        {
            $listId = (int) $documentlist->first('documentlist_distribution', true);

            if (!strlen($listId))
            {
                continue;
            }

            $folderId = $documentlist->first('documentlist_folder', true);
            if (strlen($folderId))
            {
                $folder           = $folderRepository->create();
                $folder->listId   = $listId;
                $folder->folderId = $folderId;
                $folder->eid      = (int) $eid;
                $folderRepository->save($folder);
            }
        }
    }
}
