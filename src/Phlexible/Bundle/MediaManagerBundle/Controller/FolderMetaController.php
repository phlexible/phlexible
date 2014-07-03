<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\MediaSiteBundle\Folder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Folder meta controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/foldermeta")
 * @Security("is_granted('media')")
 */
class FolderMetaController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="mediamanager_folder_meta")
     */
    public function metaAction(Request $request)
    {
        $folderId = $request->get('folder_id');

        $metaResolver = $this->get('mediamanager.meta.resolver');
        $meta = array(); //$metaResolver->getFolderMeta($folderId);

        return new JsonResponse(array('meta' => $meta));
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="mediamanager_folder_meta_save")
     */
    public function saveAction(Request $request)
    {
        $folderId = $request->get('folder_id');
        $data = $request->get('data');
        $data = json_decode($data, true);

        $registry = $container->registry;
        $languagesManager = $container->languagesManager;
        $dispatcher = $container->dispatcher;
        $metaManager = $container->mediaSiteFolderMetaManager;

        $metaDefaultLanguage = $registry->getValue('system.languages.language.meta');
        $metaLanguages = $languagesManager->getSet('meta');

        $site = Media_Site_Manager::getInstance()->getByFolderId($folderId);
        $folder = $site->getFolderPeer()->getByID($folderId);

        $beforeEvent = new Media_Manager_Event_BeforeSaveFolderMeta($folder);
        if ($dispatcher->dispatch($beforeEvent) === false) {
            $this->getResponse()->setAjaxPayload(
                MWF_Ext_Result::encode(false, null, $beforeEvent->getCancelReason())
            );

            return;
        }

        $metaSetItems = $metaManager->getMetaSetItems($folderId, $metaDefaultLanguage);

        foreach ($data as $key => $row) {
            if ('suggest' === $metaSetItems[$row['set_id']]->getType($key)) {
                $dataSourceId = $metaSetItems[$row['set_id']]->getOptions($key);
                $dataSourcesRepository = $container->get('dataSourcesRepository');
                $dataSource = $dataSourcesRepository->getDataSourceById(
                    $dataSourceId,
                    $metaDefaultLanguage
                );
                $dataSourceKeys = $dataSource->getKeys();
                $dataSourceModified = false;
                foreach (explode(',', $row['value_' . $metaDefaultLanguage]) as $singleValue) {
                    if (!in_array($singleValue, $dataSourceKeys)) {
                        $dataSource->addKey($singleValue, true);
                        $dataSourceModified = true;
                    }
                }
                if ($dataSourceModified) {
                    $dataSourcesRepository->save($dataSource, MWF_Env::getUid());
                }
            }

            $metaSetItems[$row['set_id']]->$key = $row['value_' . $metaDefaultLanguage];
        }

        foreach ($metaSetItems as $metaSetItem) {
            $metaSetItem->save();
        }

        unset($metaSetItems);

        foreach ($metaLanguages as $metaLanguage) {
            if ($metaLanguage === $metaDefaultLanguage) {
                continue;
            }

            $metaSetItems = $metaManager->getMetaSetItems($folderId, $metaLanguage);

            foreach ($data as $key => $row) {
                if ('suggest' === $metaSetItems[$row['set_id']]->getType($key)) {
                    $dataSourceId = $metaSetItems[$row['set_id']]->getOptions($key);
                    $dataSourcesRepository = $container->get('dataSourcesRepository');
                    $dataSource = $dataSourcesRepository->getDataSourceById(
                        $dataSourceId,
                        $metaLanguage
                    );
                    $dataSourceKeys = $dataSource->getKeys();
                    $dataSourceModified = false;
                    foreach (explode(',', $row['value_' . $metaLanguage]) as $singleValue) {
                        if (!in_array($singleValue, $dataSourceKeys)) {
                            $dataSource->addKey($singleValue, true);
                            $dataSourceModified = true;
                        }
                    }
                    if ($dataSourceModified) {
                        $dataSourcesRepository->save($dataSource, MWF_Env::getUid());
                    }
                }

                $metaSetItems[$row['set_id']]->$key = $row['value_' . $metaLanguage];
            }

            foreach ($metaSetItems as $metaSetItem) {
                $metaSetItem->save();
            }
        }

        $event = new Media_Manager_Event_SaveFolderMeta($folder);
        $dispatcher->dispatch($event);

        $db = $container->dbPool->write;
        $updateData = array(
            'modify_user_id' => MWF_Env::getUid(),
            'modify_time'    => $db->fn->now(),
        );
        $where = array(
            'id = ?' => $folder->getId(),
        );
        $db->update(
            $db->prefix . 'mediamanager_folders',
            $updateData,
            $where
        );

        return new ResultResponse(true, 'Meta saved.');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/listsets", name="mediamanager_folder_meta_listsets")
     */
    public function listsetsAction(Request $request)
    {
        $folderId = $request->get('folder_id');

        $metaManager = $this->get('mediaSiteFolderMetaManager');

        $metaSetItems = $metaManager->getMetaSetItems($folderId);

        $sets = array();
        foreach ($metaSetItems as $metaSetKey => $metaSetItem) {
            $metaSet = $this->getContainer()->get('metasets.repository')->find($metaSetKey);

            $sets[] = array(
                'set_id' => $metaSetKey,
                'name'   => $metaSet->getTitle(),
            );
        }

        return new JsonResponse(array('sets' => $sets));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/availablesets", name="mediamanager_folder_meta_availablesets")
     */
    public function availablesetsAction(Request $request)
    {
        $folderId = $request->get('folder_id');

        $metaManager = $this->get('mediaSiteFolderMetaManager');

        $allMetaSets = $this->get('metasets.repository')->findAll();
        $metaSetItems = $metaManager->getMetaSetItems($folderId);

        foreach ($allMetaSets as $metaSetKey => $metaSet) {
            if (isset($metaSetItems[$metaSetKey])) {
                unset($allMetaSets[$metaSetKey]);
            }
        }

        $sets = array();
        foreach ($allMetaSets as $metaSetKey => $metaSet) {
            $sets[] = array(
                'set_id' => $metaSetKey,
                'name'   => $metaSet->getTitle(),
            );
        }

        return new JsonResponse(array('sets' => $sets));
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/addset", name="mediamanager_folder_meta_addset")
     */
    public function addsetAction(Request $request)
    {
        $folderId = $request->get('folder_id');
        $setId = $request->get('set_id');

        $metaManager = $this->get('mediaSiteFolderMetaManager');

        $metaSet = $this->get('metasets.repository')->find($setId);
        $metaManager->addMetaSet($folderId, $metaSet);

        $db = $container->dbPool->write;

        $updateData = array(
            'modify_user_id' => MWF_Env::getUid(),
            'modify_time'    => $db->fn->now(),
        );

        $db->update(
            $db->prefix . 'mediamanager_folders',
            $updateData,
            array('id = ?' => $folderId)
        );

        return new ResultResponse(true, 'Set added.');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/removeset", name="mediamanager_folder_meta_removeset")
     */
    public function removesetAction(Request $request)
    {
        $folderId = $request->get('folder_id');
        $setId = $request->get('set_id');

        $metaManager = $this->get('mediaSiteFolderMetaManager');

        $metaSet = $this->getContainer()->get('metasets.repository')->find($setId);
        $metaManager->removeMetaSet($folderId, $metaSet);

        $db = $container->dbPool->write;
        $updateData = array(
            'modify_user_id' => MWF_Env::getUid(),
            'modify_time'    => $db->fn->now(),
        );
        $db->update(
            $db->prefix . 'mediamanager_folders',
            $updateData,
            array('id = ?' => $folderId)
        );

        return new ResultResponse(true, 'Set removed.');
    }

}
