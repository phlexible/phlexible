<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\MediaManagerBundle\Event\GetSlotsEvent;
use Phlexible\Bundle\MediaManagerBundle\MediaManagerEvents;
use Phlexible\Bundle\MediaManagerBundle\Slot\SiteSlot;
use Phlexible\Bundle\MediaManagerBundle\Slot\Slots;
use Phlexible\Bundle\MediaSiteBundle\Exception\AlreadyExistsException;
use Phlexible\Bundle\MediaSiteBundle\Folder\SizeCalculator;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Phlexible\Bundle\MediaSiteBundle\Site;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Folder controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/folder")
 * @Security("is_granted('media')")
 */
class FolderController extends Controller
{
    /**
     * @param string $siteId
     *
     * @return SiteInterface
     */
    private function getSite($siteId = null)
    {
        $siteManager = $this->get('phlexible_media_site.site_manager');

        if ($siteId) {
            return $siteManager->getSiteById($siteId);
        }

        return current($siteManager->getAll());
    }

    /**
     * @param FolderInterface $folder
     *
     * @return array
     */
    private function recurseFolders(FolderInterface $folder)
    {
        $site = $folder->getSite();
        $subFolders = $site->findFoldersByParentFolder($folder);

        $securityContext = $this->get('security.context');
        $permissions = $this->get('phlexible_access_control.permissions');

        $children = array();
        foreach ($subFolders as $subFolder) {
            /* @var $subFolder FolderInterface */

            if (!$securityContext->isGranted('FOLDER_READ', $folder)) {
                continue;
            }

            // TODO: rights
            /*
            $userRights = $subFolder->getRights(MWF_Env::getUser());
            if (null === $userRights) {
                continue;
            }
            $userRights = array('FOLDER_READ', 'FOLDER_CREATE', 'FOLDER_MODIFY', 'FOLDER_DELETE', 'FOLDER_RIGHTS', 'FILE_READ', 'FILE_CREATE', 'FILE_MODIFY', 'FILE_DELETE', 'FILE_DOWNLOAD');
            */
            $userRights = array_keys($permissions->getByType('folder-internal'));

            $tmp = array(
                'id'        => $subFolder->getId(),
                'text'      => $subFolder->getName(),
                'leaf'      => false,
                'numChilds' => $site->countFilesByFolder($subFolder),
                'draggable' => true,
                'expanded'  => true,
                'allowDrop' => true,
                'rights'    => $userRights,
            );

            if ($site->countFoldersByParentFolder($subFolder)) {
                $tmp['children'] = $this->recurseFolders($subFolder);
                $tmp['expanded'] = false;
            } else {
                $tmp['children'] = array();
                $tmp['expanded'] = true;
            }

            $children[] = $tmp;
        }

        return $children;
    }

    /**
     * List folders
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/list", name="mediamanager_folder_list")
     */
    public function listAction(Request $request)
    {
        $folderId = $request->get('node', null);

        $data = array();

        $slots = new Slots();
        $siteManager = $this->get('phlexible_media_site.site_manager');
        $dispatcher = $this->get('event_dispatcher');
        $securityContext = $this->get('security.context');
        $permissions = $this->get('phlexible_access_control.permissions');

        if (!$folderId || $folderId === 'root') {
            foreach ($siteManager->getAll() as $site) {
                $rootFolder = $site->findRootFolder();

                if (!$securityContext->isGranted('FOLDER_READ', $rootFolder)) {
                    continue;
                }

                // TODO: rights
                /*
                $userRights = $rootFolder->getRights(MWF_Env::getUser());
                if (null === $userRights)
                {
                    continue;
                }
                $userRights = array('FOLDER_READ', 'FOLDER_CREATE', 'FOLDER_MODIFY', 'FOLDER_DELETE', 'FOLDER_RIGHTS', 'FILE_READ', 'FILE_CREATE', 'FILE_MODIFY', 'FILE_DELETE', 'FILE_DOWNLOAD');
                */
                $userRights = array_keys($permissions->getByType('folder-internal'));

                $slot = new SiteSlot();
                $slot->setData(
                    array(
                        array(
                            'id'        => $rootFolder->getId(),
                            'site_id'   => $site->getId(),
                            'text'      => $rootFolder->getName(),
                            'cls'       => 't-mediamanager-root',
                            'leaf'      => !$site->countFoldersByParentFolder($rootFolder),
                            'numChilds' => $site->countFilesByFolder($rootFolder),
                            'draggable' => false,
                            'expanded'  => true,
                            'allowDrop' => true,
                            'versions'  => $site->hasFeature('versions'),
                            'rights'    => $userRights,
                        )
                    )
                );

                $slots->append($slot);
            }

            $event = new GetSlotsEvent($slots);
            $dispatcher->dispatch(MediaManagerEvents::GET_SLOTS, $event);

            $data = $slots->getAllData();

            //            $data[] = array(
            //                'id'        => 'tags',
            //                'text'      => 'Tags',
            //                'iconCls'   => 'p-mediamanager-tag-icon',
            //                'cls'       => 't-mediamanager-root',
            //                'leaf'      => false,
            //                'children' => array(array(
            //                    'id' => 'tag1',
            //                    'text' => 'tag1',
            //                    'leaf' => true
            //                )),
            //                'draggable' => false,
            //                'expanded'  => true,
            //                'allowDrag' => false,
            //                'allowDrop' => false,
            //                'module'    => true,
            //            );
        } else {
            $slotKey = $request->get('slot', null);
            if (!$slotKey) {
                $site = $siteManager->getByFolderId($folderId);
                $folder = $site->findFolder($folderId);

                if (!$securityContext->isGranted('FOLDER_READ', $folder)) {
                    return;
                }

                foreach ($site->findFoldersByParentFolder($folder) as $subFolder) {
                    if (!$securityContext->isGranted('FOLDER_READ', $subFolder)) {
                        continue;
                    }

                    /*
                    $userRights = $subFolder->getRights(MWF_Env::getUser());
                    if (null === $userRights)
                    {
                        continue;
                    }
                    $userRights = array();
                    */
                    $userRights = array_keys($permissions->getByType('folder-internal'));;

                    $folderUsageService = $this->get('phlexible_media_manager.folder_usage_manager');
                    $usage = $folderUsageService->getStatus($folder);
                    $usedIn = $folderUsageService->getUsedIn($folder);
                    // TODO: also files in folder!

                    $tmp = array(
                        'id'        => $subFolder->getId(),
                        'site_id'   => $site->getId(),
                        'text'      => $subFolder->getName(),
                        'leaf'      => false,
                        'numChilds' => $site->countFilesByFolder($subFolder),
                        'allowDrop' => true,
                        'versions'  => $site->hasFeature('versions'),
                        'rights'    => $userRights,
                        'used_in'   => $usedIn,
                        'used'      => $usage,
                    );

                    if (!$site->countFoldersByParentFolder($subFolder)) {
                        $tmp['leaf'] = true;
                        $tmp['expanded'] = true;
                        $tmp['children'] = array();
                    }

                    $data[] = $tmp;
                }
            } else {
                $slots = new Slots();

                $event = new GetSlotsEvent($slots);
                $dispatcher->dispatch($event);

                $slot = $slots->getSlot($slotKey);

                $data = $slot->getData(false);
            }
        }

        return new JsonResponse($data);
    }

    /**
     * Create new folder
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="mediamanager_folder_create")
     */
    public function createAction(Request $request)
    {
        $parentId = $request->get('parent_id');
        $name = $request->get('folder_name');

        $site = $this->get('phlexible_media_site.site_manager')->getByFolderId($parentId);
        $parentFolder = $site->findFolder($parentId);

        try {
            $folder = $site->createFolder($parentFolder, $name, $this->getUser()->getId());

            return new ResultResponse(true, 'Folder created.', array(
                'folder_id'   => $folder->getId(),
                'folder_name' => $folder->getName()
            ));
        } catch (AlreadyExistsException $e) {
            return new ResultResponse(false, $e->getMessage(), array(
                'folder_name' => 'Folder already exists.'
            ));
        } catch (\Exception $e) {
            return new ResultResponse(false, $e->getMessage());
        }
    }

    /**
     * Rename folder
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/rename", name="mediamanager_folder_rename")
     */
    public function renameAction(Request $request)
    {
        $siteId = $request->get('site_id');
        $folderId = $request->get('folder_id');
        $folderName = $request->get('folder_name');

        $site = $this->getSite($siteId);
        $folder = $site->getFolderById($folderId);

        $site->rename($folder, $folderName, $this->getUser()->getId());

        return new ResultResponse(true, 'Folder renamed.', array(
            'folder_name' => $folderName
        ));
    }

    /**
     * Delete folder
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/delete", name="mediamanager_folder_delete")
     */
    public function deleteAction(Request $request)
    {
        $siteId = $request->get('site_id');
        $folderId = $request->get('folder_id');

        $site = $this->getSite($siteId);
        $folder = $site->findFolder($folderId);

        if ($folder->isRoot()) {
            return new ResultResponse(false, "Can't delete the root folder.");
        }

        $site->deleteFolder($folder, $this->getUser()->getId());

        return new ResultResponse(true);
    }

    /**
     * Move folder
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/move", name="mediamanager_folder_move")
     */
    public function moveAction(Request $request)
    {
        $siteId = $request->get('site_id');
        $targetId = $request->get('target_id');
        $sourceId = $request->get('source_id');

        $site = $this->getSite($siteId);
        $folder = $site->getFolder($sourceId);
        $targetFolder = $site->getFolder($targetId);

        $site->move($folder, $targetFolder, $this->getUser()->getId());

        return new ResultResponse(true);
    }

    /**
     * Folder properties
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/properties", name="mediamanager_folder_properties")
     */
    public function propertiesAction(Request $request)
    {
        $folderId = $request->get('folder_id');
        $siteManager = $this->get('phlexible_media_site.site_manager');
        $site = $siteManager->getByFolderId($folderId);

        try {
            $folder = $site->findFolder($folderId);

            $calculator = new SizeCalculator();
            list($size, $files, $folders) = $calculator->calculate($site, $folder);

            $data = array(
                'title'       => $folder->getName(),
                'type'        => 'folder',
                'path'        => '/' . $folder->getPath(),
                'size'        => $size,
                'files'       => $files,
                'folders'     => $folders,
                'create_time' => $folder->getCreatedAt()->format('U') * 1000,
                'create_user' => $folder->getCreateUserId(),
                'modify_time' => $folder->getModifiedAt()->format('U') * 1000,
                'modify_user' => $folder->getModifyUserId(),
            );
        } catch (\Exception $e) {
            $data = array();
        }

        return new JsonResponse($data);
    }
}
