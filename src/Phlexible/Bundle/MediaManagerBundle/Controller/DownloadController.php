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

use Alchemy\Zippy\Zippy;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Component\Volume\Model\FolderIterator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Download controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/download")
 * @Security("is_granted('ROLE_MEDIA')")
 */
class DownloadController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/file", name="mediamanager_download_file")
     */
    public function fileAction(Request $request)
    {
        $fileId = $request->get('id');
        $fileVersion = $request->get('version', null);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $volume = $volumeManager->getByFileId($fileId);

        if ($fileVersion) {
            $file = $volume->findFile($fileId, $fileVersion);
        } else {
            $file = $volume->findFile($fileId);
        }

        $filepath = $file->getPhysicalPath();
        $filename = $file->getName();

        $response = new BinaryFileResponse($filepath, 200, array('Content-Type' => $file->getMimeType()));
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/folder/zip", name="mediamanager_download_folder_zip")
     */
    public function folderZipAction(Request $request)
    {
        $folderId = $request->get('folder_id');

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $path = $this->container->getParameter('phlexible_media_manager.temp_dir');

        $filesystem = new Filesystem();
        $filesystem->mkdir($path);

        $volume = $volumeManager->getByFolderId($folderId);
        $folder = $volume->findFolder($folderId);

        $prefix = $folder->getPath();
        $prefixLength = 0;
        if ($prefix && mb_strpos($prefix, '/') !== false) {
            $prefix = mb_substr($prefix, 0, -mb_strlen('/' . $folder->getName()));
            $prefixLength = mb_strlen($prefix) + 1;
            $prefix = '';
        } elseif ($folder->isRoot()) {
            $prefix = $folder->getName() . '/';
        } else {
            $prefix = '';
        }

        $filename = 'folder_' . $folder->getName() . '_' . date('YmdHis');
        $filename = preg_replace('/[^a-zA-Z0-9-_]/', '_', $filename);
        $filename = $path . $filename . '.zip';

        $rii = new \RecursiveIteratorIterator(new FolderIterator($folder), \RecursiveIteratorIterator::SELF_FIRST);

        $files = [];
        foreach ($rii as $folder) {
            $folderPath = $folder->getPath() . '/';
            foreach ($volume->findFilesByFolder($folder) as $file) {
                $displayName = $prefix . $folderPath . $file->getName();
                $displayName = mb_substr($displayName, $prefixLength, null, 'UTF-8');
                $files[$displayName] = $file->getPhysicalPath();
            }
        }

        $zippy = Zippy::load();
        $zippy->create($filename, $files);

        return new ResultResponse(true, 'Zip finished', ['filename' => basename($filename)]);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/file/zip", name="mediamanager_download_file_zip")
     */
    public function fileZipAction(Request $request)
    {
        $fileIds = $request->get('data');
        $fileIds = json_decode($fileIds);

        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $path = $this->container->getParameter('phlexible_media_manager.temp_dir');

        $filesystem = new Filesystem();
        $filesystem->mkdir($path);

        $firstFileId = current($fileIds);
        $volume = $volumeManager->getByFileId($firstFileId);
        $firstFile = $volume->findFile($firstFileId);
        $folder = $volume->findFolder($firstFile->getFolderId());

        $filename = $path . 'files_' . $folder->getName() . '_' . date('YmdHis') . '.zip';

        $files = [];
        foreach ($fileIds as $fileId) {
            $file = $volume->findFile($fileId);

            $files[$folder->getName() . '/' . $file->getName()] = $file->getPhysicalPath();
        }

        $zippy = Zippy::load();
        $zippy->create($filename, $files);

        return new ResultResponse(true, 'Zip finished', ['filename' => basename($filename)]);
    }

    /**
     * Stream file
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/zip", name="mediamanager_download_zip")
     */
    public function zipAction(Request $request)
    {
        $filename = basename($request->get('filename'));

        $path = $this->container->getParameter('phlexible_media_manager.temp_dir');
        $filepath = $path .'/' . $filename;

        if (!$filename || !file_exists($filepath)) {
            return $this->createNotFoundException();
        }

        $response = new BinaryFileResponse($filepath, 200, array('Content-Type' => 'application/zip'));
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }
}
