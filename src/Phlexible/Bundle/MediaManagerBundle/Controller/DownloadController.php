<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Alchemy\Zippy\Zippy;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderIterator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Download controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/download")
 * @Security("is_granted('media')")
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

        $siteManager = $this->get('phlexible_media_site.site_manager');
        $site = $siteManager->getByFileId($fileId);

        if ($fileVersion) {
            $file = $site->findFile($fileId, $fileVersion);
        } else {
            $file = $site->findFile($fileId);
        }

        $filepath = $file->getPhysicalPath();
        $filename = $file->getName();

        return $this->get('igorw_file_serve.response_factory')
            ->create($filepath, $file->getMimeType(), array(
                'serve_filename' => $filename,
                'absolute_path'  => true,
                'inline'         => false,
            ));
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

        $siteManager = $this->get('phlexible_media_site.site_manager');
        $path = $this->container->getParameter('phlexible_media_manager.temp_dir');

        $filesystem = new Filesystem();
        $filesystem->mkdir($path);

        $site = $siteManager->getByFolderId($folderId);
        $folder = $site->findFolder($folderId);

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

        $files = array();
        foreach ($rii as $folder) {
            $folderPath = $folder->getPath() . '/';
            foreach ($site->findFilesByFolder($folder) as $file) {
                $displayName = $prefix . $folderPath . $file->getName();
                $displayName = substr($displayName, $prefixLength);
                $files[$displayName] = $file->getPhysicalPath();
            }
        }

        $zippy = Zippy::load();
        $zippy->create($filename, $files);

        return new ResultResponse(true, 'Zip finished', array('filename' => basename($filename)));
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

        $siteManager = $this->get('phlexible_media_site.site_manager');
        $path = $this->container->getParameter('phlexible_media_manager.temp_dir');

        $filesystem = new Filesystem();
        $filesystem->mkdir($path);

        $firstFileId = current($fileIds);
        $site = $siteManager->getByFileId($firstFileId);
        $firstFile = $site->findFile($firstFileId);
        $folder = $site->findFolder($firstFile->getFolderId());

        $filename = $path . 'files_' . $folder->getName() . '_' . date('YmdHis') . '.zip';

        $files = array();
        foreach ($fileIds as $fileId) {
            $file = $site->findFile($fileId);

            $files[$folder->getName() . '/' . $file->getName()] = $file->getPhysicalPath();
        }

        $zippy = Zippy::load();
        $zippy->create($filename, $files);

        return new ResultResponse(true, 'Zip finished', array('filename' => basename($filename)));
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

        return $this->get('igorw_file_serve.response_factory')
            ->create($filepath, 'application/zip', array(
                'absolute_path' => true,
                'inline'        => false,
            ));
    }
}
