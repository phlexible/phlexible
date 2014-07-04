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

        $folder = $this->get('phlexible_media_site.manager')->getByFolderId($folderId)->findFolder($folderId);

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $folderMetaDataManager = $this->get('phlexible_media_manager.folder_meta_data_manager');

        $meta = array();
        foreach ($folder->getAttribute('metasets', array()) as $metaSetId) {
            $metaSet = $metaSetManager->find($metaSetId);
            $metaData = $folderMetaDataManager->findByMetaSetAndFolder($metaSet, $folder);

            $fieldDatas = array();
            foreach ($metaSet->getFields() as $field) {
                $fieldData = array(
                    'key'          => $field->getName(),
                    'type'         => $field->getType(),
                    'options'      => $field->getOptions(),
                    'readonly'     => $field->isReadonly(),
                    'required'     => $field->isRequired(),
                    'synchronized' => $field->isSynchronized(),
                );
                foreach ($metaData->getLanguages() as $language) {
                    $fieldData["value_$language"] = $metaData->get($field->getName(), $language);
                }
                $fieldDatas[] = $fieldData;
            }

            $meta[] = array(
                'set_id' => $metaSetId,
                'title'  => $metaSet->getName(),
                'fields' => $fieldDatas
            );
        }

        return new JsonResponse(array('meta' => $meta));
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

        $siteManager = $this->get('phlexible_media_site.manager');
        $folderMetaSetResolver = $this->get('phlexible_media_manager.folder_meta_set_resolver');

        $folder = $siteManager->getByFolderId($folderId)->findFolder($folderId);
        $metaSets = $folderMetaSetResolver->resolve($folder);

        $sets = array();
        foreach ($metaSets as $metaSet) {
            $sets[] = array(
                'set_id' => $metaSet->getId(),
                'name'   => $metaSet->getName(),
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

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $folderMetaSetResolver = $this->get('phlexible_media_manager.folder_meta_set_resolver');
        $siteManager = $this->get('phlexible_media_site.manager');

        $folder = $siteManager->getByFolderId($folderId)->findFolder($folderId);

        $metaSets = $metaSetManager->findAll();
        foreach ($folderMetaSetResolver->resolve($folder) as $index => $metaSet) {
            if (in_array($metaSet, $metaSets)) {
                unset($metaSets[$index]);
            }
        }

        $sets = array();
        foreach ($metaSets as $metaSet) {
            $sets[] = array(
                'set_id' => $metaSet->getId(),
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

        $siteManager = $this->get('phlexible_media_site.manager');

        $site = $siteManager->getByFolderId($folderId);
        $folder = $site->findFolder($folderId);

        $attributes = $folder->getAttributes();
        if (!isset($attributes['metasets'])) {
            $attributes['metasets'] = array();
        }
        if (!in_array($setId, $attributes['metasets'])) {
            $attributes['metasets'][] = $setId;
            $site->setFolderAttributes($folder, $attributes);
        }

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

        $siteManager = $this->get('phlexible_media_site.manager');

        $site = $siteManager->getByFolderId($folderId);
        $folder = $site->findFolder($folderId);

        $attributes = $folder->getAttributes();
        if (isset($attributes['metasets']) && in_array($setId, $attributes['metasets'])) {
            unset($attributes['metasets'][array_search($setId, $attributes['metasets'])]);
            $site->setFolderAttributes($folder, $attributes);
        }

        return new ResultResponse(true, 'Set removed.');
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
}
