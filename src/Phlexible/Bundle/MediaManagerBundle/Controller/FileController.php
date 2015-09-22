<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\Volume\Exception\NotFoundException;
use Phlexible\Component\Volume\VolumeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * File controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/file")
 * @Security("is_granted('ROLE_MEDIA')")
 */
class FileController extends Controller
{
    /**
     * @param string $folderId
     *
     * @return VolumeInterface
     */
    private function getVolumeByFolderId($folderId)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        return $volumeManager->getByFolderId($folderId);
    }

    /**
     * @param string $str
     * @param bool   $capitaliseFirstChar
     *
     * @return string
     */
    private function toCamelCase($str, $capitaliseFirstChar = true)
    {
        if ($capitaliseFirstChar) {
            $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');

        return lcfirst(preg_replace_callback('/_([a-z])/', $func, $str));
    }

    /**
     * List files
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/list", name="mediamanager_file_list")
     */
    public function listAction(Request $request)
    {
        $folderId = $request->get('folder_id');
        $start = $request->get('start', 0);
        $limit = $request->get('limit', 25);
        $sort = $request->get('sort', 'name');
        $dir = $request->get('dir', 'ASC');
        $showHidden = $request->get('show_hidden', false);
        $filter = $request->get('filter');

        if (!$folderId) {
            throw new \RuntimeException("No folder ID");
        }

        if ($filter) {
            $filter = json_decode($filter, true);
        }

        $data = [];
        $total = 0;

        $volume = $this->getVolumeByFolderId($folderId);
        $securityContext = $this->get('security.context');

        $folder = $volume->findFolder($folderId);

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN') || $securityContext->isGranted('FILE_READ', $folder)) {
            if ($sort === 'create_time') {
                $sort = 'created_at';
            } elseif ($sort === 'document_type_key') {
                $sort = 'mime_type';
            }

            if ($filter) {
                $filter['folder'] = $folder;
                if (!$showHidden) {
                    $filter['hidden'] = false;
                }
                if (!empty($filter['assetType'])) {
                    $filter['mediaCategory'] = $filter['assetType'];
                    unset($filter['assetType']);
                }
                if (!empty($filter['documenttypeType'])) {
                    $filter['mediaType'] = $filter['documenttypeType'];
                    unset($filter['documenttypeType']);
                }
                $files = $volume->findFiles($filter, [$this->toCamelCase($sort) => $dir], $limit, $start);
                $total = $volume->countFiles($filter);
            } else {
                $files = $volume->findFilesByFolder($folder, [$this->toCamelCase($sort) => $dir], $limit, $start, $showHidden);
                $total = $volume->countFilesByFolder($folder, $showHidden);
            }

            $data = $this->filesToArray($volume, $files);
        }

        return new JsonResponse(['files' => $data, 'totalCount' => $total]);
    }

    /**
     * Build file list
     *
     * @param VolumeInterface         $volume
     * @param ExtendedFileInterface[] $files
     *
     * @return array
     */
    private function filesToArray(VolumeInterface $volume, array $files)
    {
        $data = [];
        $userManager = $this->get('phlexible_user.user_manager');
        $mediaTypeManager = $this->get('phlexible_media_type.media_type_manager');

        $hasVersions = $volume->hasFeature('versions');

        foreach ($files as $file) {
            try {
                $createUser = $userManager->find($file->getCreateUserId());
                $createUserName = $createUser->getDisplayName();
            } catch (\Exception $e) {
                $createUserName = 'Unknown';
            }

            try {
                if ($file->getModifyUserId()) {
                    $modifyUser = $userManager->find($file->getModifyUserId());
                    $modifyUserName = $modifyUser->getDisplayName();
                } else {
                    $modifyUserName = 'Unknown';
                }
            } catch (\Exception $e) {
                $modifyUserName = 'Unknown';
            }

            $properties = [
                //'attributes'    => array(),
                //'attributesCnt' => 0,
                'versions' => $hasVersions,
                'debug'    => [
                    'mimeType'      => $file->getMimeType(),
                    'mediaCategory' => strtolower($file->getMediaCategory()),
                    'mediaType'     => strtolower($file->getMediaType()),
                    'fileId'        => $file->getID(),
                    'folderId'      => $file->getFolderId(),
                ]
            ];

            $meta = [];
            // TODO: enable
            //foreach ($asset->getMetas()->getAll() as $metaData) {
            //    foreach ($metaData->getValues() as $key => $value) {
            //        $meta[$metaData->getTitle()][$key] = $value;
            //    }
            //}
            $properties['meta'] = $meta;
            $properties['metaCnt'] = count($properties['meta']);

            $mediaType = $mediaTypeManager->find(strtolower($file->getMediaType()));

            if (!$mediaType) {
                $mediaType = $mediaTypeManager->create();
                $mediaType->setName('unknown');
            }

            $interfaceLanguage = $this->getUser()->getInterfaceLanguage('en');
            $mediaTypeTitle = $mediaType->getTitle($interfaceLanguage);

            $version = 1;
            if ($hasVersions) {
                $version = $file->getVersion();
            }

            $cacheItems = $this->get('phlexible_media_cache.cache_manager')->findByFile($file->getID(), $version);
            $cache = [];
            foreach ($cacheItems as $cacheItem) {
                if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                    $cache[$cacheItem->getTemplateKey()] = $this->generateUrl('mediamanager_media', [
                        'file_id'      => $file->getId(),
                        'file_version' => $file->getVersion(),
                        'template_key' => $cacheItem->getTemplateKey(),
                    ]);
                } else {
                    $cache[$cacheItem->getTemplateKey()] = $this->generateUrl('mediamanager_media_delegate', [
                        'mediaTypeName' => $file->getMediaType(),
                        'templateKey'   => $cacheItem->getTemplateKey(),
                    ]);
                }
            }

            $fileUsageManager = $this->get('phlexible_media_manager.file_usage_manager');
            $usage = $fileUsageManager->getStatus($file);
            $usedIn = $fileUsageManager->getUsedIn($file);

            $focal = 0;
            if ($file->getAttribute('focalpoint')) {
                $focal = 1;
            }

            $attributes = $file->getAttributes();

            $folder = $volume->findFolder($file->getFolderId());
            $data[] = [
                'id'                => $file->getID(),
                'name'              => $file->getName(),
                'site_id'           => $volume->getId(),
                'folder_id'         => $file->getFolderID(),
                'folder'            => '/Root/' . $folder->getPath(),
                'asset_type'        => strtolower($file->getMediaCategory()),
                'mime_type'         => $file->getMimetype(),
                'document_type'     => $mediaTypeTitle,
                'document_type_key' => strtolower($file->getMediaType()),
                'present'           => file_exists($file->getPhysicalPath()),
                'size'              => $file->getSize(),
                'hidden'            => $file->isHidden() ? 1 : 0,
                'version'           => $version,
                'create_user'       => $createUserName,
                'create_user_id'    => $file->getCreateUserId(),
                'create_time'       => $file->getCreatedAt()->format('Y-m-d H:i:s'),
                'modify_user'       => $modifyUserName,
                'modify_user_id'    => $file->getModifyUserId(),
                'modify_time'       => $file->getModifiedAt() ? $file->getModifiedAt()->format('Y-m-d H:i:s') : null,
                'cache'             => $cache,
                'properties'        => $properties,
                'used_in'           => $usedIn,
                'used'              => $usage,
                'focal'             => $focal,
                'attributes'        => !empty($attributes['parsed']) ? $attributes['parsed'] : array(),
            ];
        }

        return $data;
    }

    /**
     * Delete File
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/delete", name="mediamanager_file_delete")
     */
    public function deleteAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $fileIds = explode(',', $fileId);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        foreach ($fileIds as $fileId) {
            try {
                $volume = $volumeManager->getByFileId($fileId);
                $file = $volume->findFile($fileId);
                if ($file) {
                    $volume->deleteFile($file, $this->getUser()->getId());
                }
            } catch (NotFoundException $e) {
            }
        }

        return new ResultResponse(true, count($fileIds) . ' file(s) deleted.');
    }

    /**
     * Hide File
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/hide", name="mediamanager_file_hide")
     */
    public function hideAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $fileIds = explode(',', $fileId);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        foreach ($fileIds as $fileId) {
            $volume = $volumeManager->getByFileId($fileId);
            $file = $volume->findFile($fileId);
            if ($file) {
                $volume->hideFile($file, $this->getUser()->getId());
            }
        }

        return new ResultResponse(true, count($fileIds) . ' file(s) hidden.');
    }

    /**
     * Show file
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/show", name="mediamanager_file_show")
     */
    public function showAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $fileIds = explode(',', $fileId);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        foreach ($fileIds as $fileId) {
            $volume = $volumeManager->getByFileId($fileId);
            $file = $volume->findFile($fileId);
            if ($file) {
                $volume->showFile($file, $this->getUser()->getId());
            }
        }

        return new ResultResponse(true, count($fileIds) . ' file(s) shown.');
    }

    /**
     * Properties
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/properties", name="mediamanager_file_properties")
     */
    public function propertiesAction(Request $request)
    {
        $fileId = $request->get('id');
        $fileVersion = $request->get('version', 1);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        $volume = $volumeManager->getByFileId($fileId);
        $file = $volume->findFile($fileId, $fileVersion);
        $folder = $volume->findFolder($file->getFolderId());

        $attributes = $file->getAttributes();

        $versions = [];
        if ($volume->hasFeature('versions')) {
            $versions = $volume->findFileVersions($file);
        }

        $properties = [];
        $properties['id'] = $fileId;
        $properties['version'] = $fileVersion;
        $properties['path'] = '/' . $folder->getPath();
        $properties['name'] = $file->getName();
        $properties['size'] = $file->getSize();
        $properties['document_type_key'] = strtolower($file->getMediaType());
        $properties['asset_type'] = strtolower($file->getMediaCategory());
        $properties['create_user_id'] = $file->getCreateUserId();
        $properties['create_time'] = $file->getCreatedAt()->format('U');

        $properties['attributes'] = !empty($attributes['parsed']) ? $attributes['parsed'] : array();
        $properties['attributesCnt'] = count(!empty($attributes['parsed']) ? $attributes['parsed'] : array());

        $properties['versions'] = $versions;
        $properties['versionsCnt'] = count($properties['versions']);

        $properties['keywords'] = [];
        $properties['keywordsCnt'] = count($properties['keywords']);

        $properties['debug'] = [
            'mimeType'     => $file->getMimeType(),
            'documentType' => strtolower($file->getMediaType()),
            'assetType'    => strtolower($file->getMediaCategory()),
            'fileId'       => $fileId,
            'folderId'     => $folder->getId(),
        ];

        $properties['detail'] = [
            'id'                => $file->getId(),
            'folder_id'         => $file->getFolderId(),
            'name'              => $file->getName(),
            'size'              => $file->getSize(),
            'version'           => $file->getVersion(),
            'document_type_key' => strtolower($file->getMediaType()),
            'asset_type'        => strtolower($file->getMediaCategory()),
            'create_user_id'    => $file->getCreateUserId(),
            'create_time'       => $file->getCreatedAt()->format('Y-m-d'),
        ];

        /*
        $previousFile = $site->findPreviousFile($file, 'name ASC');
        $nextFile = $site->findNextFile($file, 'name ASC');
        */

        $properties['prev'] = null;
        if (!empty($previousFile)) {
            $properties['prev'] = [
                'file_id'      => $previousFile->getId(),
                'file_version' => $previousFile->getVersion(),
            ];
        }

        $properties['next'] = null;
        if (!empty($nextFile)) {
            $properties['next'] = [
                'file_id'      => $nextFile->getId(),
                'file_version' => $nextFile->getVrsion(),
            ];
        }

        return new JsonResponse($properties);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/detail", name="mediamanager_file_detail")
     */
    public function detailAction(Request $request)
    {
        $id = $request->get('id');

        $volume = $this->get('phlexible_media_manager.volume_manager')->getByFileId($id);

        $detail = [];
        foreach ($volume->findFileVersions($id) as $file) {
            $detail[] = [
                'id'                => $file->getId(),
                'folder_id'         => $file->getFolderId(),
                'name'              => $file->getName(),
                'size'              => $file->getSize(),
                'version'           => $file->getVersion(),
                'document_type_key' => strtolower($file->getMediaType()),
                'asset_type'        => strtolower($file->getMediaCategory()),
                'create_user_id'    => $file->getCreateUserId(),
                'create_time'       => $file->getCreatedAt()->format('Y-m-d'),
            ];
        }

        return new JsonResponse(
            [
                'detail' => $detail
            ]
        );
    }

    /**
     * Copy file
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/copy", name="mediamanager_file_copy")
     */
    public function copyAction(Request $request)
    {
        $folderId = $request->get('folderID');
        $fileIDs = json_decode($request->get('fileIDs'));

        $volume = $this->getVolumeByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        foreach ($fileIDs as $fileID) {
            $file = $volume->findFile($fileID);
            $volume->copyFile($file, $folder, $this->getUser()->getId());
        }

        return new ResultResponse(true, 'File(s) copied.');
    }

    /**
     * Move file
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/move", name="mediamanager_file_move")
     */
    public function moveAction(Request $request)
    {
        $folderId = $request->get('folderID');
        $fileIds = json_decode($request->get('fileIDs'));

        $volume = $this->getVolumeByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        $skippedFiles = [];

        foreach ($fileIds as $fileId) {
            $file = $volume->findFile($fileId);
            try {
                $volume->moveFile($file, $folder, $this->getUser()->getId());
            } catch (AlreadyExistsException $e) {
                $skippedFiles[] = $file->getName();
            }
        }

        return new ResultResponse(true, 'File(s) moved.', $skippedFiles);
    }

    /**
     * Rename file
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/rename", name="mediamanager_file_rename")
     */
    public function renameAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $name = $request->get('file_name');

        $volume = $this->get('phlexible_media_manager.volume_manager')->getByFileId($fileId);

        $file = $volume->findFile($fileId);
        $volume->renameFile($file, $name, $this->getUser()->getId());

        return new ResultResponse(true, 'File(s) renamed.');
    }
}
