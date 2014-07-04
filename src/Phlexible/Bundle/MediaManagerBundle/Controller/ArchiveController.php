<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Archive controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/archive")
 * @Security("is_granted('media')")
 */
class ArchiveController extends Controller
{
    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @throws \Exception
     * @Route("/zip", name="mediamanager_archive_zip")
     */
    public function zipAction(Request $request)
    {
        $mediaSiteManager = $this->get('phlexible_media_site.manager');

        $folderID = $request->get('folder_id');
        $site = $mediaSiteManager->getByFolderId($folderID);
        $folder = $site->getFolderPeer()->getByID($folderID);

        $path = $this->container->getParameter(':media.manager.temp_dir');
        $filename = $folder->getName() . '_' . date('YmdHis');
        $filename = preg_replace('/[^a-zA-Z0-9-_]/', '_', $filename);
        $filename = $path . $filename . '.zip';

        if (!file_exists($path)) {
            if (!mkdir($path, 0777, true)) {
                throw new \Exception('Can\'t create zip temp dir: ' . $path);
            }
        }

        $zip = new \ZipArchive();
        $zip->open($filename, \ZipArchive::CREATE);

        $this->addFolderToZip($folder, $zip);

        $zip->close();

        return new ResultResponse(true, 'Zip finished', array('filename' => basename($filename)));
    }

    private function addFolderToZip(FolderInterface $folder, \ZipArchive $zip, $namePrefix = '')
    {
        $files = $folder->getFiles();

        foreach ($files as $file) {
            if (!is_file($file->getFilePath())) {
                $this->getContainer()->get('logger')->warn(__METHOD__ . ' File not found: ' . $file->getFilePath());
                continue;
            }

            $zip->addFile($file->getFilePath(), $namePrefix . $file->getName());
        }

        $subFolders = $folder->getFolders();

        foreach ($subFolders as $subFolder) {
            $this->addFolderToZip($subFolder, $zip, $subFolder->getName() . '/');
        }
    }
}
