<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Controller;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFolderInterface;
use Phlexible\Component\Volume\VolumeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Folder controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/frontendmedia/folder")
 */
class FolderController extends Controller
{
    /**
     * Deliver a media asset
     *
     * @return JsonResponse
     * @Route("/tree", name="frontendmedia_folder")
     */
    public function treeAction()
    {
        $data = [];

        $securityContext = $this->get('security.context');

        foreach ($this->get('phlexible_media_manager.volume_manager')->all() as $volume) {
            $rootFolder = $volume->findRootFolder();

            if (!$securityContext->isGranted('FOLDER_READ', $rootFolder)) {
                // TODO: uncomment
                //continue;
            }

            $data[] = [
                'id'        => $rootFolder->getId(),
                'site_id'   => $volume->getId(),
                'text'      => $rootFolder->getName(),
                'leaf'      => !$volume->countFoldersByParentFolder($rootFolder),
                'draggable' => false,
                'expanded'  => true,
                'allowDrop' => true,
                'children'  => $this->recurseFolders($volume, $rootFolder),
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @param VolumeInterface         $volume
     * @param ExtendedFolderInterface $folder
     *
     * @return array
     */
    private function recurseFolders(VolumeInterface $volume, ExtendedFolderInterface $folder)
    {
        $data = [];

        $securityContext = $this->get('security.context');

        foreach ($volume->findFoldersByParentFolder($folder) as $subFolder) {
            if (!$securityContext->isGranted('FOLDER_READ', $subFolder)) {
                // TODO: uncomment
                //continue;
            }

            $tmp = [
                'id'        => $subFolder->getId(),
                'site_id'   => $volume->getId(),
                'text'      => $subFolder->getName(),
                'leaf'      => false, //!$subFolder->hasSubFolders(),
                'numChilds' => $volume->countFoldersByParentFolder($subFolder),
                'allowDrop' => true,
                'children'  => $this->recurseFolders($volume, $subFolder),
            ];

            if (!$tmp['numChilds']) {
                $tmp['expanded'] = true;
                $tmp['children'] = [];
            }

            $data[] = $tmp;
        }

        return $data;
    }
}
