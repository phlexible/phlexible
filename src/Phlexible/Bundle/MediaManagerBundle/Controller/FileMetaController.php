<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

        $file = $this->get('phlexible_media_manager.volume_manager')->getByFileId($fileId)->findFile($fileId, $fileVersion);

        $fileMetaSetResolver = $this->get('phlexible_media_manager.file_meta_set_resolver');
        $fileMetaDataManager = $this->get('phlexible_media_manager.file_meta_data_manager');
        $optionResolver = $this->get('phlexible_meta_set.option_resolver');

        $meta = [];

        foreach ($fileMetaSetResolver->resolve($file) as $metaSet) {
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
                'set_id' => $metaSet->getId(),
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

        $volume = $this->get('phlexible_media_manager.volume_manager')->getByFileId($fileId);
        $file = $volume->findFile($fileId, $fileVersion);

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
                $metaData = $fileMetaDataManager->createMetaData($metaSet);
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

            $fileMetaDataManager->updateMetaData($file, $metaData);
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

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $fileMetaSetResolver = $this->get('phlexible_media_manager.file_meta_set_resolver');

        $file = $volumeManager->getByFileId($fileId)->findFile($fileId, $fileVersion);
        $metaSets = $fileMetaSetResolver->resolve($file);

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

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId, $fileVersion);
        $volume->setFileMetasets($file, $ids, $this->getUser()->getId());

        return new ResultResponse(true, 'Set added.');
    }
}
