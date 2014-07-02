<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\MediaManagerBundle\MediaManagerMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Upload controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/upload")
 * @Security("is_granted('debug')")
 */
class UploadController extends Controller
{
    /**
     * Upload File
     *
     * @param Request $request
     *
     * @Route("", name="mediamanager_upload")
     */
    public function uploadAction(Request $request)
    {
        $folderId = $request->get('folder_id', null);

        try {
            $siteManager = $this->get('mediasite.manager');
            $site = $siteManager->getByFolderId($folderId);
            $folder = $site->findFolder($folderId);

            if (empty($folder)) {
                return new ResultResponse(
                    false,
                    'Target folder not found.',
                    array(
                        'params'  => $request->request->all(),
                        'files'   => $request->files->all(),
                    )
                );
            }

            if (!$request->files->count()) {
                return new ResultResponse(
                    false,
                    'No files received.',
                    array(
                        'params'  => $request->request->all(),
                        'files'   => $request->files->all(),
                    )
                );
            }

            $uploadHandler = $this->get('mediamanager.upload.handler');

            $cnt = 0;
            foreach ($request->files->all() as $uploadedFile) {
                $file = $uploadHandler->handle($uploadedFile, $folderId, $this->getUser()->getId());

                if ($file) {
                    $cnt++;

                    $body = 'Filename: '.$file->getName() . PHP_EOL
                        . 'Folder:   '.$folder->getName() . PHP_EOL
                        . 'Filesize: '.$file->getSize() . PHP_EOL
                        . 'Filetype: '.$file->getMimeType() . PHP_EOL;

                    $message = MediaManagerMessage::create('File "'.$file->getName().'" uploaded.', $body);
                    $this->get('phlexible_message.message_poster')->post($message);
                }
            }

            return new ResultResponse(
                true,
                $cnt . ' file(s) uploaded.',
                array(
                    'params'  => $request->request->all(),
                    'files'   => $request->files->all(),
                )
            );
        } catch (\Exception $e) {
            return new ResultResponse(
                false,
                $e->getMessage(),
                array(
                    'params'  => $request->request->all(),
                    'files'   => $request->files->all(),
                    'trace'   => $e->getTraceAsString(),
                )
            );
        }
    }

    /**
     * @return JsonResponse
     * @Route("/check", name="mediamanager_upload_check")
     */
    public function checkAction()
    {
        $tempStorage = $this->get('mediamanager.upload.storage.temp');
        $siteManager = $this->get('mediasite.manager');
        $documenttypeRepository = $this->get('documenttypes.repository');

        $data = array();

        if ($tempStorage->count()) {
            $useWizard = 0;

            foreach ($tempStorage->getAll() as $tempFile) {
                $site = $siteManager->getByFolderId($tempFile->getFolderId());
                $supportsVersions = $site->hasFeature('versions');
                $folder = $site->findFolder($tempFile->getFolderId());
                $newName  = basename($tempFile->getName());
                $mimetype = $this->getContainer()->get('mediaToolsMime')->detect($tempFile->getTempName());
                $newType  = $documenttypeRepository->getByMimetype($mimetype)->getKey();

                $temp = array(
                    'versions' => $supportsVersions,
                    'temp_key' => $tempFile->getId(),
                    'temp_id'  => $tempFile->getId(),
                    'new_id'   => $tempFile->getOriginalFileId(),
                    'new_name' => $newName,
                    'new_type' => $newType,
                    'new_size' => $tempFile->getSize()
                );

                if ($tempFile->getOriginalFileId()) {
                    $oldFile = $site->findFile($tempFile->getOriginalFileId());

                    $alternativeName = $this->createAlternateFilename($tempFile->getTempName(), $folder);

                    $temp['old_name']         = $tempFile['name'];
                    $temp['old_id']           = $tempFile['original_id'];
                    $temp['old_type']         = $oldFile->getDocumentTypeKey();
                    $temp['old_size']         = $oldFile->getSize();
                    $temp['alternative_name'] = $alternativeName;
                }

                if (!empty($tempFile['file_id'])) {
                    $temp['file_id'] = $tempFile['file_id'];
                }

                if (!empty($tempFile['use_wizard'])) {
                    $useWizard = true;
                }

                if (!empty($tempFile['parsed'])) {
                    $temp['parsed'] = $tempFile['parsed'];
                }

                $data['files'][] = $temp;
            }

            if ($useWizard) {
                $data['wizard'] = 1;
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @throws Media_Site_Folder_Exception
     * @Route("/save", name="mediamanager_upload_save")
     */
    public function saveAction(Request $request)
    {
        $all     = $request->get('all');
        $action  = $request->get('do');
        $tempKey = $request->get('temp_key');
        $tempID  = $request->get('temp_id');

        $metaSetId = $request->get('metaset', null);
        $metaData  = $request->get('meta', null);
        if ($metaData) {
            $metaData = json_decode($metaData, true);
        }

        die(__METHOD__." session");
        $uploadTempSession = new Zend_Session_Namespace('uploadTemp');

        $container        = $this->getContainer();
        $mediaSiteManager = $container->get('mediasite.manager');

        if (isset($uploadTempSession->siteId)) {
            $site = $mediaSiteManager->getSiteById($uploadTempSession->siteId);
        } else {
            $site = $mediaSiteManager->get('mediamanager');
        }

        $tempFiles = $uploadTempSession->files[$tempKey];

        $data  = array();
        if (!$all)
        {
            if (!empty($tempFiles[$tempID]))
            {
                $tempFile = $tempFiles[$tempID];

                unset($uploadTempSession->files[$tempKey][$tempID]);
                /* @var $folder Media_Site_Folder_Abstract */
                $folder = $site->getFolderPeer()->getByID($tempFile['folder_id']);

                if (!empty($tempFile['original_id'])) {
                    $file = $site->getFilePeer()->getByID($tempFile['original_id']);
                }

                switch($action) {
                    case 'save':
                        try {
                            $newFile = $folder->importFile($tempFile['tmp_name'], $tempFile['name']);
                        } catch (\Exception $e) {
                            $this->getContainer()->get('logger')->error(__METHOD__ . ' save: '.$e->getMessage().PHP_EOL.$e->getTraceAsString());

                            throw new Media_Site_Folder_Exception($e->getMessage());
                        }

                        break;

                    case 'replace':
                        try {
                            $dispatcher = $container->get('event_dispatcher');

                            // post before replace file event
                            $event = new Media_Site_Event_BeforeReplaceFile($site, $folder, $file);
                            if (false === $dispatcher->dispatch($event)) {
                                throw new Media_Site_Folder_Exception(
                                    'Can\'t replace file "'
                                        . $file->getFilePath()
                                        . '", callback returned false.'
                                );
                            }

                            // replace file on disc
                            if (!copy($tempFile['tmp_name'], $file->getFilePath())) {
                                throw new Media_Site_Folder_Exception('Copy failed.');
                            }

                            // re-read file information
                            $file->reRead();

                            // queue mediacache item regeneration
                            $queueBatch = new Media_Cache_Queue_Batch();
                            $queueBatch->add(null, array($file->getId()));

                            // post replace file event
                            $event = new Media_Site_Event_ReplaceFile($site, $folder, $file);
                            $dispatcher->dispatch($event);

                            $newFile = $file;
                        } catch (Exception $e) {
                            $this->getContainer()->get('logger')->error(__METHOD__ . ' replace: '.$e->getMessage().PHP_EOL.$e->getTraceAsString());

                            throw new Media_Site_Folder_Exception($e->getMessage());
                        }

                        break;

                    case 'keep':
                        try {
                            $newName = $this->_getAlternativeFilename($tempFile['name'], $folder);

                            $newFile = $folder->importFile($tempFile['tmp_name'], $newName);
                        } catch (\Exception $e) {
                            $this->getContainer()->get('logger')->error(__METHOD__ . ' keep: '.$e->getMessage().PHP_EOL.$e->getTraceAsString());

                            throw new Media_Site_Folder_Exception($e->getMessage());
                        }

                        break;

                    case 'version':
                        try {
                            $fileId = $tempFile['original_id'];
                            if (!empty($tempFile['file_id']))
                            {
                                $fileId = $tempFile['file_id'];
                            }

                            $newFile = $folder->importFileVersion($tempFile['tmp_name'], $tempFile['name'], $fileId);
                        } catch (Exception $e) {
                            $this->getContainer()->get('logger')->error(__METHOD__ . ' version: '.$e->getMessage().PHP_EOL.$e->getTraceAsString());

                            throw new Media_Site_Folder_Exception($e->getMessage());
                        }

                        break;

                    case 'discard':
                    default:

                        break;
                }

                $dirname = dirname($tempFile['tmp_name']);
                if (file_exists($tempFile['tmp_name'])) {
                    unlink($tempFile['tmp_name']);
                }
                if (!glob($dirname . '/*')) {
                    rmdir($dirname);
                }

                if ($newFile) {
                    try {
                        $asset = $newFile->getAsset();
                    } catch(Exception $e) {
                    }

                    if ($metaSetId) {
                        try {
                            $metaSet = $this->getContainer()->get('metasets.repository')->find($metaSetId);
                            $asset->addMetaSet($metaSet);
                        } catch (Exception $e) {
                            $this->getContainer()->get('logger')->error(__METHOD__ . ' newFile: '.$e->getMessage().PHP_EOL.$e->getTraceAsString());
                        }
                    }

                    if ($metaData) {
                        foreach (array('de', 'en') as $language) {
                            $metaSetItems = $asset->getMetaSetItems($language);

                            foreach ($metaData as $key => $row) {
                                $metaSetItems[$row['set_id']]->$key = $row['value_' . $language];
                            }

                            foreach ($metaSetItems as $metaSetItem) {
                                $metaSetItem->save();
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($tempFiles as $tempID => $tempFile) {
                unset($uploadTempSession->files[$tempKey][$tempID]);

                $folder = $site->getFolderPeer()->getByID($tempFile['folder_id']);

                if (!empty($tempFile['original_id'])) {
                    $file = $site->getFilePeer()->getByID($tempFile['original_id']);
                }

                switch($action) {
                    case 'save':
                        try {
                            $newFileID = $folder->importFile($tempFile['tmp_name'], $tempFile['name']);
                        } catch (\Exception $e) {
                            $this->getContainer()->get('logger')->error(__METHOD__ . ' save: '.$e->getMessage().PHP_EOL.$e->getTraceAsString());

                            throw new Media_Site_Folder_Exception($e->getMessage());
                        }

                        break;

                    case 'replace':
                        try {
                            if (!copy($tempFile['tmp_name'], $file->getFilePath())) {
                                throw new Media_Site_Folder_Exception('Copy failed.');
                            }

                            $file->reRead();
                        } catch (Exception $e) {
                            $this->getContainer()->get('logger')->error(__METHOD__ . ' replace: '.$e->getMessage().PHP_EOL.$e->getTraceAsString());

                            throw new Media_Site_Folder_Exception($e->getMessage());
                        }

                        break;

                    case 'keep':
                        try {
                            $newName = $this->_getAlternativeFilename($tempFile['name'], $folder);

                            $newFileID = $folder->importFile($tempFile['tmp_name'], $newName);
                        } catch (Exception $e) {
                            $this->getContainer()->get('logger')->error(__METHOD__ . ' keep: '.$e->getMessage().PHP_EOL.$e->getTraceAsString());

                            throw new Media_Site_Folder_Exception($e->getMessage());
                        }

                        break;

                    case 'discard':
                    default:

                        break;
                }

                $dirname = dirname($tempFile['tmp_name']);
                if (file_exists($tempFile['tmp_name'])) {
                    unlink($tempFile['tmp_name']);
                }
                if (!glob($dirname . '/*')) {
                    rmdir($dirname);
                }
            }
        }

        if (!count($tempFiles)) {
            unset($uploadTempSession->files[$tempKey]);
        }

        return new ResultResponse(true);
    }

    /**
     * @return Response
     * @Route("/preview", name="mediamanager_upload_preview")
     */
    public function previewAction()
    {
        $tempKey = $this->_getParam('key');
        $tempId = $this->_getParam('id');
        $templateKey = $this->_getParam('template');

        $template = $this->getContainer()->get('mediatemplates.repository')->find($templateKey);

        die(__METHOD__." session");
        $uploadTempSession = new Zend_Session_Namespace('uploadTemp');

        if (isset($uploadTempSession->siteId)) {
            $site = Media_Site_Manager::getInstance()->getSiteById($uploadTempSession->siteId);
        } else {
            $site = Media_Site_Manager::getInstance()->get('mediamanager');
        }

        $tempFiles = $uploadTempSession->files[$tempKey];
        $tempFile = $tempFiles[$tempId];

        $toolkit = $template->getAppliedToolkit($tempFile['tmp_name']);
        $filename = $toolkit->save($this->getContainer()->getParam(':media.manager.temp_dir') . 'upload_preview/', true);

        return $this->get('igorw_file_serve.response_factory')
                    ->create($filename);
    }

    /**
     * @return JsonResponse
     * @Route("/metasets", name="mediamanager_upload_metasets")
     */
    public function metasetsAction()
    {
        $allSets = $this->getContainer()->get('metasets.repository')->getAll();

        $sets = array();
        foreach($allSets as $key => $set) {
            $sets[] = array(
                'key'   => $key,
                'title' => $set->getTitle()
            );
        }

        return new JsonResponse(array('metasets' => $sets));
    }

    /**
     * @return JsonResponse
     * @Route("/meta", name="mediamanager_upload_meta")
     */
    public function metaAction()
    {
        $additionalMetaSet = $this->_getParam('metaset', null);

        $metaSet = $this->getContainer()->get('metasets.repository')->find('document');
        $keys = $metaSet->getKeys();

        $meta = array();

        $t9n  = $this->getContainer()->t9n;
        $pageKeys = $t9n->{'metadata-keys'}->toArray();
        $pageSelect = $t9n->{'metadata-selectvalues'}->toArray();

        $container = $this->getContainer();
        $dataSourceRepository = $container->get('dataSourcesRepository');

        foreach($keys as $key => $row) {
            $meta[$key] = $row;
            $meta[$key]['set_id'] = $metaSet->getId();
            $meta[$key]['key']    = $key;
            $meta[$key]['value_de']  = '';
            $meta[$key]['value_en']  = '';
            $meta[$key]['required'] = (int) $meta[$key]['required'];

            $meta[$key]['tkey'] = $key;
            if (!empty($pageKeys[$key])) {
                $meta[$key]['tkey'] = $pageKeys[$key];
            }

            if ($row['type'] == 'select') {
                $options = explode(',', $row['options']);

                foreach ($options as $k => $okey) {
                    $okey   = trim($okey);
                    $value = $okey;
                    if (!empty($pageSelect[$okey]))
                    {
                        $value = $pageSelect[$okey];
                    }
                    $options[$k] = array($okey, $value);
                }

                $meta[$key]['options'] = $options;
            } elseif ($row['type'] == 'suggest') {
                $sourceId = $row['options'];
                $options  = array('source_id' => $sourceId);

                foreach (array('de', 'en') as $language) {
                    $source = $dataSourceRepository->getDataSourceById(
                        $sourceId,
                        $language
                    );

                    $keys = $source->getKeys();

                    foreach ($keys as $value) {
                        $options["values_$language"][] = array($value, $value);
                    }

                }

                $meta[$key]['options'] = $options;
            }
        }

        if ($additionalMetaSet) {
            try {
                $metaSet = $this->getContainer()->get('metasets.repository')->find($additionalMetaSet);
                $keys    = $metaSet->getKeys();

                foreach($keys as $key => $row) {
                    $meta[$key] = $row;
                    $meta[$key]['set_id'] = $metaSet->getId();
                    $meta[$key]['key']   = $key;
                    $meta[$key]['value_de'] = '';
                    $meta[$key]['value_en'] = '';
                    $meta[$key]['required'] = (int) $meta[$key]['required'];

                    $meta[$key]['tkey'] = $key;
                    if (!empty($pageKeys[$key])) {
                        $meta[$key]['tkey'] = $pageKeys[$key];
                    }

                    if ($row['type'] == 'select') {
                        $options = explode(',', $row['options']);

                        foreach ($options as $k => $okey) {
                            $okey   = trim($okey);
                            $value = $okey;
                            if (!empty($pageSelect[$okey]))
                            {
                                $value = $pageSelect[$okey];
                            }
                            $options[$k] = array($okey, $value);
                        }

                        $meta[$key]['options'] = $options;
                    } elseif ($row['type'] == 'suggest') {
                        $sourceId = $row['options'];
                        $options  = array('source_id' => $sourceId);

                        foreach (array('de', 'en') as $language) {
                            $source = $dataSourceRepository->getDataSourceById(
                                $sourceId,
                                $language
                            );

                            $keys = $source->getKeys();

                            foreach ($keys as $value) {
                                $options["values_$language"][] = array($value, $value);
                            }
                        }

                        $meta[$key]['options'] = $options;
                    }
                }
            } catch (\Exception $e) {

            }
        }

        $meta = array_values($meta);

        return new JsonResponse($meta);
    }

    private function createAlternateFilename($filename, $folder)
    {
        $newNameParts   = pathinfo($filename);
        $newNameFormat  = basename($newNameParts['basename'], '.'.$newNameParts['extension']);
        $newNameFormat .= '(%s).' . $newNameParts['extension'];

        $i = 1;

        do {
            $i++;
            $newName = sprintf($newNameFormat, $i);
        } while ($folder->hasFile($newName));

        return $newName;
    }
}
