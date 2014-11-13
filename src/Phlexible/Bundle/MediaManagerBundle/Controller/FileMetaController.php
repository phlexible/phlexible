<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * File meta controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/filemeta")
 * @Security("is_granted('ROLE_MEDIA')")
 */
class FileMetaController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="mediamanager_file_meta")
     */
    public function metaAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $fileVersion = $request->get('file_version', 1);

        $file = $this->get('phlexible_media_site.site_manager')->getByFileId($fileId)->findFile($fileId, $fileVersion);

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $fileMetaDataManager = $this->get('phlexible_media_manager.file_meta_data_manager');
        $optionResolver = $this->get('phlexible_meta_set.option_resolver');

        $meta = [];

        foreach ($file->getAttribute('metasets', []) as $metaSetId) {
            $metaSet = $metaSetManager->find($metaSetId);
            $metaData = $fileMetaDataManager->findByMetaSetAndFile($metaSet, $file);

            $fieldDatas = [];

            foreach ($metaSet->getFields() as $field) {
                $options = $optionResolver->resolve($field);

                $fieldData = [
                    'key'          => $field->getName(),
                    'type'         => $field->getType(),
                    'options'      => $options,
                    'readonly'     => $field->isReadonly(),
                    'required'     => $field->isRequired(),
                    'synchronized' => $field->isSynchronized(),
                ];

                if ($metaData) {
                    foreach ($metaData->getLanguages() as $language) {
                        $fieldData["value_$language"] = $metaData->get($field->getName(), $language);
                    }
                }

                $fieldDatas[] = $fieldData;
            }

            $meta[] = [
                'set_id' => $metaSetId,
                'title'  => $metaSet->getName(),
                'fields' => $fieldDatas
            ];
        }

        return new JsonResponse(['meta' => $meta]);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="mediamanager_file_meta_save")
     */
    public function saveAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $fileVersion = $request->get('file_version', 1);
        $data = $request->get('data');
        $data = json_decode($data, true);

        $metaLanguages = explode(',', $this->container->getParameter('phlexible_meta_set.languages.available'));

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $fileMetaDataManager = $this->get('phlexible_media_manager.file_meta_data_manager');

        $site = $this->get('phlexible_media_site.site_manager')->getByFileId($fileId);
        $file = $site->findFile($fileId, $fileVersion);

        /*
        $beforeEvent = new BeforeSaveFileMeta($file);
        if ($dispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
            $this->getResponse()->setAjaxPayload(
                MWF_Ext_Result::encode(false, null, $beforeEvent->getCancelReason())
            );

            return;
        }
        */

        $metaSetIds = $file->getAttribute('metasets', []);

        foreach ($data as $metaSetId => $fields) {
            $metaSet = $metaSetManager->find($metaSetId);
            $metaData = $fileMetaDataManager->findByMetaSetAndFile($metaSet, $file);

            if (!$metaData) {
                $metaData = $fileMetaDataManager->createFileMetaData($metaSet, $file);
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

            $fileMetaDataManager->updateMetaData($metaData);
        }

        /*
        $event = new Media_Manager_Event_SaveFileMeta($file);
        $dispatcher->dispatch($event);
        */

        return new ResultResponse(true, 'File meta saved.');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/listsets", name="mediamanager_file_meta_sets_list")
     */
    public function listsetsAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $fileVersion = $request->get('file_version', 1);

        $siteManager = $this->get('phlexible_media_site.site_manager');
        $fileMetaSetResolver = $this->get('phlexible_media_manager.file_meta_set_resolver');

        $folder = $siteManager->getByFileId($fileId)->findFile($fileId, $fileVersion);
        $metaSets = $fileMetaSetResolver->resolve($folder);

        $sets = [];
        foreach ($metaSets as $metaSet) {
            $sets[] = [
                'id'   => $metaSet->getId(),
                'name' => $metaSet->getName(),
            ];
        }

        return new JsonResponse(['sets' => $sets]);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/savesets", name="mediamanager_file_meta_sets_save")
     */
    public function savesetsAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $fileVersion = $request->get('file_version', 1);
        $joinedIds = $request->get('ids');
        if ($joinedIds) {
            $ids = explode(',', $joinedIds);
        } else {
            $ids = [];
        }

        $siteManager = $this->get('phlexible_media_site.site_manager');

        $site = $siteManager->getByFileId($fileId);
        $file = $site->findFile($fileId, $fileVersion);

        $attributes = $file->getAttributes();
        $attributes->set('metasets', $ids);
        $site->setFileAttributes($file, $attributes, $this->getUser()->getId());

        return new ResultResponse(true, 'Set added.');
    }
}
