<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Controller;

use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;
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
        $data = array();

        $securityContext = $this->get('security.context');

        foreach ($this->get('phlexible_media_site.site_manager')->getAll() as $site) {
            $rootFolder = $site->findRootFolder();

            if (!$securityContext->isGranted('FOLDER_READ', $rootFolder)) {
                // TODO: uncomment
                //continue;
            }

            $data[] = array(
                'id'        => $rootFolder->getId(),
                'site_id'   => $site->getId(),
                'text'      => $rootFolder->getName(),
                'leaf'      => !$site->countFoldersByParentFolder($rootFolder),
                'draggable' => false,
                'expanded'  => true,
                'allowDrop' => true,
                'children'  => $this->recurseFolders($site, $rootFolder),
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param SiteInterface   $site
     * @param FolderInterface $folder
     *
     * @return array
     */
    private function recurseFolders(SiteInterface $site, FolderInterface $folder)
    {
        $data = array();

        $securityContext = $this->get('security.context');

        foreach ($site->findFoldersByParentFolder($folder) as $subFolder) {
            if (!$securityContext->isGranted('FOLDER_READ', $subFolder)) {
                // TODO: uncomment
                //continue;
            }

            $tmp = array(
                'id'        => $subFolder->getId(),
                'site_id'   => $site->getId(),
                'text'      => $subFolder->getName(),
                'leaf'      => false, //!$subFolder->hasSubFolders(),
                'numChilds' => $site->countFoldersByParentFolder($subFolder),
                'allowDrop' => true,
                'children'  => $this->recurseFolders($site, $subFolder),
            );

            if (!$tmp['numChilds']) {
                $tmp['expanded'] = true;
                $tmp['children'] = array();
            }

            $data[] = $tmp;
        }

        return $data;
    }
}
