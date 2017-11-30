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
use Phlexible\Bundle\MediaManagerBundle\Entity\File;
use Phlexible\Bundle\MediaManagerBundle\Event\CheckFileUploadEvent;
use Phlexible\Bundle\MediaManagerBundle\MediaManagerEvents;
use Phlexible\Bundle\MediaManagerBundle\MediaManagerMessage;
use Phlexible\Component\MetaSet\Model\MetaSet;
use Phlexible\Component\Volume\Model\FileInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Upload controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/upload")
 * @Security("is_granted('ROLE_MEDIA')")
 */
class UploadController extends Controller
{
    /**
     * Upload File.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("", name="mediamanager_upload")
     */
    public function uploadAction(Request $request)
    {
        $folderId = $request->get('folder_id', null);

        try {
            $volumeManager = $this->get('phlexible_media_manager.volume_manager');
            $volume = $volumeManager->getByFolderId($folderId);
            $folder = $volume->findFolder($folderId);

            if (empty($folder)) {
                return new ResultResponse(
                    false,
                    'Target folder not found.',
                    [
                        'params' => $request->request->all(),
                        'files' => $request->files->all(),
                    ]
                );
            }

            if (!$request->files->count()) {
                return new ResultResponse(
                    false,
                    'No files received.',
                    [
                        'params' => $request->request->all(),
                        'files' => $request->files->all(),
                    ]
                );
            }

            $uploadHandler = $this->get('phlexible_media_manager.upload.handler');

            $cnt = 0;
            foreach ($request->files->all() as $uploadedFile) {
                /* @var $uploadedFile UploadedFile */

                $file = $uploadHandler->handle($uploadedFile, $folderId, $this->getUser()->getId());

                if ($file) {
                    ++$cnt;

                    $body = 'Filename: '.$uploadedFile->getClientOriginalName().PHP_EOL
                        .'Folder:   '.$folder->getName().PHP_EOL
                        .'Filesize: '.$file->getSize().PHP_EOL
                        .'Filetype: '.$file->getMimeType().PHP_EOL;

                    $message = MediaManagerMessage::create('File "'.$file->getName().'" uploaded.', $body);
                    $this->get('phlexible_message.message_poster')->post($message);
                }
            }

            return new ResultResponse(
                true,
                $cnt.' file(s) uploaded.',
                [
                    'params' => $request->request->all(),
                    'files' => $request->files->all(),
                ]
            );
        } catch (\Exception $e) {
            return new ResultResponse(
                false,
                $e->getMessage(),
                [
                    'params' => $request->request->all(),
                    'files' => $request->files->all(),
                    'trace' => $e->getTraceAsString(),
                    'traceArray' => $e->getTrace(),
                ]
            );
        }
    }

    /**
     * @return JsonResponse
     * @Route("/check", name="mediamanager_upload_check")
     */
    public function checkAction()
    {
        $tempHandler = $this->get('phlexible_media_manager.upload.temp_handler');
        $tempStorage = $this->get('phlexible_media_manager.upload.temp_storage');
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $mediaTypeManager = $this->get('phlexible_media_type.media_type_manager');
        $mediaTypeMetasetMatcher = $this->get('phlexible_media_manager.media_type_metaset_matcher');
        $metasetManager = $this->get('phlexible_meta_set.meta_set_manager');

        $data = [];

        if ($tempStorage->count()) {
            $tempFile = $tempStorage->next();
            $volume = $volumeManager->getByFolderId($tempFile->getFolderId());
            $supportsVersions = $volume->hasFeature('versions');
            $newName = basename($tempFile->getName());
            $mimetype = $tempFile->getMimeType();
            $newType = null;
            if (trim($mimetype)) {
                $newType = $mediaTypeManager->findByMimetype($mimetype);
            }
            if (!$newType) {
                $newType = $mediaTypeManager->find('binary');
            }

            $metasets = array();
            foreach ($mediaTypeMetasetMatcher->match($newType) as $metasetName) {
                $metaset = $metasetManager->findOneByName($metasetName);
                if ($metaset) {
                    $metasets[] = $metaset;
                }
            }

            $data = [
                'versions' => $supportsVersions,
                'temp_key' => $tempFile->getId(),
                'temp_id' => $tempFile->getId(),
                'new_id' => $tempFile->getFileId(),
                'new_name' => $newName,
                'new_type' => $newType->getName(),
                'new_size' => $tempFile->getSize(),
                'new_hash' => $tempFile->getHash(),
                'new_metasets' => $this->createMetasetConfig($metasets),
                'wizard' => false,
                'total' => $tempStorage->count(),
            ];

            if ($tempFile->getFileId()) {
                $oldFile = $volume->findFile($tempFile->getFileId());

                $alternativeName = $tempHandler->createAlternateFilename($tempFile, $volume);

                $data['old_name'] = $tempFile->getName();
                $data['old_id'] = $tempFile->getFileId();
                $data['old_type'] = $oldFile->getMediaType();
                $data['old_size'] = $oldFile->getSize();
                $data['old_hash'] = $oldFile->getHash();
                $data['alternative_name'] = $alternativeName;
            }

            if ($tempFile->getFileId()) {
                $data['file_id'] = $tempFile->getFileId();
            }

            if ($tempFile->getUseWizard()) {
                $data['wizard'] = true;
            }

            $event = new CheckFileUploadEvent($data);
            $this->get('event_dispatcher')->dispatch(
                MediaManagerEvents::CHECK_FILE_UPLOAD,
                $event
            );

            /*
            // TODO: parser stuff
            if (!empty($tempFile['parsed'])) {
                $data['parsed'] = [
                    'metaSetId' => [
                        'fieldName' => ['value_de' => 'abc', 'value_en' => 'def'],
                    ],
                ];
            }
            */

            $data = $event->getData();
        }

        return new JsonResponse($data);
    }

    /**
     * @return JsonResponse
     * @Route("/clear", name="mediamanager_upload_clear")
     */
    public function clearAction()
    {
        $tempStorage = $this->get('phlexible_media_manager.upload.temp_storage');
        $tempStorage->removeAll();

        return new ResultResponse(true);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="mediamanager_upload_save")
     */
    public function saveAction(Request $request)
    {
        $all = $request->get('all');
        $action = $request->get('action');
        $tempId = $request->get('temp_id');

        $metaData = $request->get('meta', null);
        if ($metaData) {
            $metaData = json_decode($metaData, true);
        }

        $tempHandler = $this->get('phlexible_media_manager.upload.temp_handler');

        if ($all) {
            $tempHandler->handleAll($action);
        } else {
            $file = $tempHandler->handle($action, $tempId);

            if ($file && $metaData) {
                $this->saveMeta($file, $metaData);
            }
        }

        return new ResultResponse(true, ($all ? 'All' : 'File').' saved with action '.$action);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/preview", name="mediamanager_upload_preview")
     */
    public function previewAction(Request $request)
    {
        $tempId = $request->get('id');
        $templateKey = $request->get('template');

        $tempStorage = $this->get('phlexible_media_manager.upload.temp_storage');
        $templateManager = $this->get('phlexible_media_template.template_manager');
        $imageApplier = $this->get('phlexible_media_template.applier.image');

        $template = $templateManager->find($templateKey);
        $tempFile = $tempStorage->get($tempId);

        $outFilename = $this->container->getParameter('phlexible_media_manager.temp_dir').'preview.png';
        $mimeType = 'image/png';
        try {
            $imageApplier->apply($template, new File(), $tempFile->getPath(), $outFilename);
        } catch (\Exception $e) {
            $delegateService = $this->get('phlexible_media_cache.image_delegate.service');
            $mediaTypeManager = $this->get('phlexible_media_type.media_type_manager');
            $outFilename = $delegateService->getClean($template, $mediaTypeManager->findByMimetype($tempFile->getMimeType()), true);
            $mimeType = 'image/gif';
        }

        $response = new BinaryFileResponse($outFilename, 200, array('Content-Type' => $mimeType));

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/set", name="mediamanager_upload_set")
     */
    public function setAction(Request $request)
    {
        $ids = $request->get('ids');
        $ids = explode(',', $ids);

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $metaSets = [];
        foreach ($ids as $id) {
            $metaSets[] = $metaSetManager->find($id);
        }

        $config = $this->createMetasetConfig($metaSets);

        return new JsonResponse($config);
    }

    /**
     * @param FileInterface $file
     * @param array         $metaData
     */
    private function saveMeta(FileInterface $file, array $metaData)
    {
        $metaLanguages = explode(',', $this->container->getParameter('phlexible_meta_set.languages.available'));

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $fileMetaDataManager = $this->get('phlexible_media_manager.file_meta_data_manager');

        $file->getVolume()->setFileMetaSets($file, array_keys($metaData), $this->getUser()->getId());

        foreach ($metaData as $metaSetId => $fields) {
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
    }

    /**
     * @param MetaSet[] $metaSets
     *
     * @return array
     */
    private function createMetasetConfig(array $metaSets)
    {
        $optionResolver = $this->get('phlexible_meta_set.option_resolver');

        $meta = array();

        foreach ($metaSets as $metaSet) {
            $fieldDatas = [];

            foreach ($metaSet->getFields() as $field) {
                $options = $optionResolver->resolve($field);

                $fieldData = [
                    'key' => $field->getName(),
                    'type' => $field->getType(),
                    'options' => $options,
                    'readonly' => $field->isReadonly(),
                    'required' => $field->isRequired(),
                    'synchronized' => $field->isSynchronized(),
                ];

                $fieldDatas[] = $fieldData;
            }

            $meta[] = [
                'set_id' => $metaSet->getId(),
                'title' => $metaSet->getName(),
                'fields' => $fieldDatas,
            ];
        }

        return $meta;
    }
}
