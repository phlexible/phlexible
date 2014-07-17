<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Alchemy\Zippy\Zippy;
use Phlexible\Bundle\DocumenttypeBundle\Model\Documenttype;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;
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
 * @Security("is_granted('media')")
 */
class FileController extends Controller
{
    /**
     * @param string $folderId
     *
     * @return SiteInterface
     */
    private function getSiteByFolderId($folderId)
    {
        $siteManager = $this->get('phlexible_media_site.manager');

        return $siteManager->getByFolderId($folderId);
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
        $folderId = $request->get('folder_id', null);
        $start = $request->get('start', 0);
        $limit = $request->get('limit', 25);
        $sort = $request->get('sort', 'name');
        $dir = $request->get('dir', 'ASC');
        $showHidden = $request->get('show_hidden', false);

        if (!$folderId) {
            die("No folder ID");
        }

        $data = array();
        $total = 0;

        $site = $this->getSiteByFolderId($folderId);
        $securityContext = $this->get('security.context');

        $folder = $site->findFolder($folderId);

        if ($securityContext->isGranted('FILE_READ', $folder)) {
            $files = $site->findFilesByFolder($folder, array($sort => $dir), $limit, $start, $showHidden);
            $total = $site->countFilesByFolder($folder, $showHidden);
            $data = $this->filesToArray($site, $files);
        }

        return new JsonResponse(array('files' => $data, 'totalCount' => $total));
    }

    /**
     * Build file list
     *
     * @param SiteInterface   $site
     * @param FileInterface[] $files
     *
     * @return array
     */
    private function filesToArray(SiteInterface $site, array $files)
    {
        $data = array();
        $userManager = $this->get('phlexible_user.user_manager');
        $documenttypeManager = $this->get('phlexible_documenttype.documenttype_manager');

        $hasVersions = $site->hasFeature('versions');

        foreach ($files as $file) {
            try {
                $createUser = $userManager->find($file->getCreateUserID());
                $createUserName = $createUser->getFirstname() . ' ' . $createUser->getLastname();
            } catch (\Exception $e) {
                $createUserName = 'Unknown';
            }

            try {
                if ($file->getModifyUserID()) {
                    $modifyUser = $userManager->find($file->getModifyUserID());
                    $modifyUserName = $modifyUser->getFirstname() . ' ' . $modifyUser->getLastname();
                } else {
                    $modifyUserName = '';
                }
            } catch (\Exception $e) {
                $modifyUserName = 'Unknown';
            }

            $properties = array(
                //'attributes'    => array(),
                //'attributesCnt' => 0,
                'versions' => $hasVersions,
                'debug'    => array(
                    'mimeType'     => $file->getMimeType(),
                    'documentType' => strtolower($file->getAttribute('documenttype')),
                    'assetType'    => strtolower($file->getAttribute('assettype')),
                    'fileId'       => $file->getID(),
                    'folderId'     => $file->getFolderId(),
                )
            );

            $meta = array();
            // TODO: enable
            //foreach ($asset->getMetas()->getAll() as $metaData) {
            //    foreach ($metaData->getValues() as $key => $value) {
            //        $meta[$metaData->getTitle()][$key] = $value;
            //    }
            //}
            $properties['meta'] = $meta;
            $properties['metaCnt'] = count($properties['meta']);

            $documentType = $documenttypeManager->find(strtolower($file->getAttribute('documenttype')));

            if (!$documentType) {
                $documentType = new Documenttype();
                $documentType->setKey('unknown');
            }

            $interfaceLanguage = $this->getUser()->getInterfaceLanguage();
            $documentTypeTitle = $documentType->getTitle($interfaceLanguage);

            $version = 1;
            if ($hasVersions) {
                $version = $file->getVersion();
            }

            $cacheItems = $this->get('phlexible_media_cache.cache_manager')->findByFile($file->getID(), $version);
            $cache = array();
            foreach ($cacheItems as $cacheItem) {
                if ($cacheItem->getStatus() === CacheItem::STATUS_OK) {
                    $cache[$cacheItem->getTemplateKey()] = $this->generateUrl('mediamanager_media', array(
                        'file_id'      => $file->getId(),
                        'file_version' => $file->getVersion(),
                        'template_key' => $cacheItem->getTemplateKey(),
                    ));
                } else {
                    $cache[$cacheItem->getTemplateKey()] = $this->generateUrl('mediamanager_media_delegate', array(
                        'documenttypeKey' => $file->getAttribute('documenttype'),
                        'templateKey'     => $cacheItem->getTemplateKey(),
                    ));
                }
            }

            $fileUsageManager = $this->get('phlexible_media_manager.file_usage_manager');
            $usage = $fileUsageManager->getStatus($file);
            $usedIn = $fileUsageManager->getUsedIn($file);

            $focal = 0;
            if ($file->getAttribute('focalpoint')) {
                $focal = 1;
            }

            $folder = $site->findFolder($file->getFolderId());
            $data[] = array(
                'id'                => $file->getID(),
                'name'              => $file->getName(),
                'site_id'           => $site->getId(),
                'folder_id'         => $file->getFolderID(),
                'folder'            => '/Root/' . $folder->getPath(),
                'asset_type'        => strtolower($file->getAttribute('assettype')),
                'mime_type'         => $file->getMimetype(),
                'document_type'     => $documentTypeTitle,
                'document_type_key' => strtolower($file->getAttribute('documenttype')),
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
            );
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

        $siteManager = $this->get('phlexible_media_site.manager');

        foreach ($fileIds as $fileId) {
            $site = $siteManager->getByFileId($fileId);
            $file = $site->findFile($fileId);
            $site->delete($file, $this->getUser()->getId());
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

        $siteManager = $this->get('phlexible_media_site.manager');

        foreach ($fileIds as $fileId) {
            $site = $siteManager->getByFileId($fileId);
            $file = $site->findFile($fileId);
            $site->hide();
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

        $siteManager = $this->get('phlexible_media_site.manager');

        foreach ($fileIds as $fileId) {
            $site = $siteManager->getByFileId($fileId);
            $file = $site->findFile($fileId);
            $site->hide($file);
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

        $siteManager = $this->get('phlexible_media_site.manager');

        $site = $siteManager->getByFileId($fileId);
        $file = $site->findFile($fileId, $fileVersion);
        $folder = $site->findFolder($file->getFolderId());

        $attributes = array();
        foreach ($file->getAttribute('attributes', array()) as $key => $value) {
            $attributes[] = array('key' => $key, 'value' => $value);
        }

        $versions = array();
        if ($site->hasFeature('versions')) {
            $versions = $site->findFileVersions($file);
        }

        $properties = array();
        $properties['id'] = $fileId;
        $properties['version'] = $fileVersion;
        $properties['path'] = '/' . $folder->getPath();
        $properties['name'] = $file->getName();
        $properties['size'] = $file->getSize();
        $properties['document_type_key'] = strtolower($file->getAttribute('documenttype'));
        $properties['asset_type'] = strtolower($file->getAttribute('assettype'));
        $properties['create_user_id'] = $file->getCreateUserId();
        $properties['create_time'] = $file->getCreatedAt()->format('U');

        $properties['attributes'] = $attributes;
        $properties['attributesCnt'] = count($properties['attributes']);

        $properties['versions'] = $versions;
        $properties['versionsCnt'] = count($properties['versions']);

        $properties['keywords'] = array();
        $properties['keywordsCnt'] = count($properties['keywords']);

        $properties['debug'] = array(
            'mimeType'     => $file->getMimeType(),
            'documentType' => strtolower($file->getAttribute('documenttype')),
            'assetType'    => strtolower($file->getAttribute('assettype')),
            'fileId'       => $fileId,
            'folderId'     => $folder->getId(),
        );

        /*
        $previousFile = $site->findPreviousFile($file, 'name ASC');
        $nextFile = $site->findNextFile($file, 'name ASC');
        */

        $properties['prev'] = null;
        if (!empty($previousFile)) {
            $properties['prev'] = array(
                'file_id'      => $previousFile->getId(),
                'file_version' => $previousFile->getVersion(),
            );
        }

        $properties['next'] = null;
        if (!empty($nextFile)) {
            $properties['next'] = array(
                'file_id'      => $nextFile->getId(),
                'file_version' => $nextFile->getVrsion(),
            );
        }

        return new JsonResponse($properties);
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

        $site = $this->getSiteByFolderId($folderId);
        $folder = $site->findFolder($folderId);

        foreach ($fileIDs as $fileID) {
            $file = $site->findFile($fileID);
            $site->copy($file, $folder, $this->getUser()->getId());
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
        $fileIDs = json_decode($request->get('fileIDs'));

        $site = $this->getSiteByFolderId($folderId);
        $folder = $site->findFolder($folderId);

        $skippedFiles = array();

        foreach ($fileIDs as $fileID) {
            $file = $site->findFile($fileID);
            if ($folder->hasFile($file->getName())) {
                $skippedFiles[] = $file->getName();
                continue;
            }
            $site->move($file, $folder, $this->getUser()->getId());
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

        $site = $this->get('phlexible_media_site.manager')->getByFileId($fileId);

        $file = $site->findFile($fileId);
        $site->renameFile($file, $name, $this->getUser()->getId());

        return new ResultResponse(true, 'File(s) renamed.');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/zip", name="mediamanager_file_zip")
     */
    public function zipAction(Request $request)
    {
        $fileIds = $request->get('data');
        $fileIds = json_decode($fileIds);

        $site = $this->get('phlexible_media_site.manager')->getByFileId(current($fileIds));
        $filename = $this->container->getParameter('media.manager.temp_dir') . 'files_' . date('YmdHis') . '.zip';

        $zippy = Zippy::load();
        $archive = $zippy->create($filename);

        foreach ($fileIds as $fileId) {
            $file = $site->findFile($fileId);

            $archive->addMembers(array($file->getName() => $file->getPhysicalPath()));
        }

        return new ResultResponse(true, 'Zip finished', array('filename' => basename($filename)));
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

        $site = $this->get('phlexible_media_site.manager')->getByFileId($id);

        $detail = array();
        foreach ($site->findFileVersions($id) as $file) {
            $detail[] = array(
                'id'                => $file->getId(),
                'folder_id'         => $file->getFolderId(),
                'name'              => $file->getName(),
                'size'              => $file->getSize(),
                'version'           => $file->getVersion(),
                'document_type_key' => strtolower($file->getAttribute('documenttype')),
                'asset_type'        => strtolower($file->getAttribute('assettype')),
                'create_user_id'    => $file->getCreateUserId(),
                'create_time'       => $file->getCreatedAt()->format('Y-m-d'),
            );
        }

        return new JsonResponse(
            array(
                'detail' => $detail
            )
        );
    }
}
