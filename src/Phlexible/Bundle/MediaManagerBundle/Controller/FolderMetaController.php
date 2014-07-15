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
        $optionResolver = $this->get('phlexible_meta_set.option_resolver');

        $meta = array();

        foreach ($folder->getAttribute('metasets', array()) as $metaSetId) {
            $metaSet = $metaSetManager->find($metaSetId);
            $metaData = $folderMetaDataManager->findByMetaSetAndFolder($metaSet, $folder);

            $fieldDatas = array();

            foreach ($metaSet->getFields() as $field) {
                $options = $optionResolver->resolve($field);

                $fieldData = array(
                    'key'          => $field->getName(),
                    'type'         => $field->getType(),
                    'options'      => $options,
                    'readonly'     => $field->isReadonly(),
                    'required'     => $field->isRequired(),
                    'synchronized' => $field->isSynchronized(),
                );

                if ($metaData) {
                    foreach ($metaData->getLanguages() as $language) {
                        $fieldData["value_$language"] = $metaData->get($field->getId(), $language);
                    }
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
     * @Route("/listsets", name="mediamanager_folder_meta_set_list")
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
     * @Route("/availablesets", name="mediamanager_folder_meta_set_available")
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
                'name'   => $metaSet->getName(),
            );
        }

        return new JsonResponse(array('sets' => $sets));
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/addset", name="mediamanager_folder_meta_set_add")
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
     * @Route("/removeset", name="mediamanager_folder_meta_set_remove")
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

        $metaLanguages = explode(',', $this->container->getParameter('phlexible_meta_set.languages.available'));

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $folderMetaDataManager = $this->get('phlexible_media_manager.folder_meta_data_manager');

        $site = $this->get('phlexible_media_site.manager')->getByFolderId($folderId);
        $folder = $site->findFolder($folderId);

        /*
        $beforeEvent = new BeforeSaveFolderMeta($folder);
        if ($dispatcher->dispatch($beforeEvent) === false) {
            $this->getResponse()->setAjaxPayload(
                MWF_Ext_Result::encode(false, null, $beforeEvent->getCancelReason())
            );

            return;
        }
        */

        $metaSetIds = $folder->getAttribute('metasets', array());

        foreach ($data as $metaSetId => $fields) {
            $metaSet = $metaSetManager->find($metaSetId);
            $metaData = $folderMetaDataManager->findByMetaSetAndFolder($metaSet, $folder);

            if (!$metaData) {
                $metaData = $folderMetaDataManager->createFolderMetaData($metaSet, $folder);
            }

            foreach ($fields as $fieldname => $row) {
                foreach ($metaLanguages as $language) {
                    if (!isset($row["value_$language"])) {
                        continue;
                    }

                    if (!$metaSet->hasField($fieldname)) {
                        continue;
                    }

                    // TODO: löschen?
                    if (empty($row["value_$language"])) {
                        continue;
                    }

                    $value = $row["value_$language"];

                    $metaData->set($fieldname, $value, $language);
                }
            }

            $folderMetaDataManager->updateMetaData($metaData);
        }

        /*
        $event = new Media_Manager_Event_SaveFolderMeta($folder);
        $dispatcher->dispatch($event);
        */

        return new ResultResponse(true, 'Folder meta saved.');
    }
}
