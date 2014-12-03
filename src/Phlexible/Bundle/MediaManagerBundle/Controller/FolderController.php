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
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFolderInterface;
use Phlexible\Component\Volume\Exception\AlreadyExistsException;
use Phlexible\Component\Volume\Folder\SizeCalculator;
use Phlexible\Component\Volume\VolumeInterface;
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
 * @Security("is_granted('ROLE_MEDIA')")
 */
class FolderController extends Controller
{
    /**
     * @param string $volumeId
     *
     * @return VolumeInterface
     */
    private function getVolume($volumeId = null)
    {
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');

        if ($volumeId) {
            return $volumeManager->getById($volumeId);
        }

        return current($volumeManager->all());
    }

    /**
     * @param ExtendedFolderInterface $folder
     *
     * @return array
     */
    private function recurseFolders(ExtendedFolderInterface $folder)
    {
        $volume = $folder->getVolume();
        $subFolders = $volume->findFoldersByParentFolder($folder);

        $securityContext = $this->get('security.context');
        $permissions = $this->get('phlexible_access_control.permissions');

        $user = $this->getUser();

        $children = [];
        foreach ($subFolders as $subFolder) {
            /* @var $subFolder ExtendedFolderInterface */

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
            $userRights = array_keys($permissions->get(get_class($subFolder), get_class($user)));

            $tmp = [
                'id'        => $subFolder->getId(),
                'text'      => $subFolder->getName(),
                'leaf'      => false,
                'numChilds' => $volume->countFilesByFolder($subFolder),
                'draggable' => true,
                'expanded'  => true,
                'allowDrop' => true,
                'allowChildren' => true,
                'isTarget'  => true,
                'rights'    => $userRights,
            ];

            if ($volume->countFoldersByParentFolder($subFolder)) {
                $tmp['children'] = $this->recurseFolders($subFolder);
                $tmp['expanded'] = false;
            } else {
                $tmp['children'] = [];
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

        $data = [];

        $slots = new Slots();
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $dispatcher = $this->get('event_dispatcher');
        $securityContext = $this->get('security.context');
        $permissions = $this->get('phlexible_access_control.permissions');

        $user = $this->getUser();

        if (!$folderId || $folderId === 'root') {
            foreach ($volumeManager->all() as $volume) {
                $rootFolder = $volume->findRootFolder();

                if (!$securityContext->isGranted('ROLE_SUPER_ADMIN') && !$securityContext->isGranted('FOLDER_READ', $rootFolder)) {
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
                $userRights = array_keys($permissions->getByContentClass(get_class($rootFolder)));

                $slot = new SiteSlot();
                $slot->setData(
                    [
                        [
                            'id'        => $rootFolder->getId(),
                            'site_id'   => $volume->getId(),
                            'text'      => $rootFolder->getName(),
                            'cls'       => 't-mediamanager-root',
                            'leaf'      => !$volume->countFoldersByParentFolder($rootFolder),
                            'numChilds' => $volume->countFilesByFolder($rootFolder),
                            'draggable' => false,
                            'expanded'  => true,
                            'allowDrop' => true,
                            'versions'  => $volume->hasFeature('versions'),
                            'rights'    => $userRights,
                        ]
                    ]
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
                $volume = $volumeManager->getByFolderId($folderId);
                $folder = $volume->findFolder($folderId);

                if (!$securityContext->isGranted('ROLE_SUPER_ADMIN') && !$securityContext->isGranted('FOLDER_READ', $rootFolder)) {
                    return new JsonResponse([]);
                }

                foreach ($volume->findFoldersByParentFolder($folder) as $subFolder) {
                    if (!$securityContext->isGranted('ROLE_SUPER_ADMIN') && !$securityContext->isGranted('FOLDER_READ', $rootFolder)) {
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
                    $userRights = array_keys($permissions->getByContentClass(get_class($subFolder)));;

                    $folderUsageService = $this->get('phlexible_media_manager.folder_usage_manager');
                    $usage = $folderUsageService->getStatus($folder);
                    $usedIn = $folderUsageService->getUsedIn($folder);
                    // TODO: also files in folder!

                    $tmp = [
                        'id'        => $subFolder->getId(),
                        'site_id'   => $volume->getId(),
                        'text'      => $subFolder->getName(),
                        'leaf'      => false,
                        'numChilds' => $volume->countFilesByFolder($subFolder),
                        'allowDrop' => true,
                        'allowChildren' => true,
                        'isTarget' => true,
                        'versions'  => $volume->hasFeature('versions'),
                        'rights'    => $userRights,
                        'used_in'   => $usedIn,
                        'used'      => $usage,
                    ];

                    if (!$volume->countFoldersByParentFolder($subFolder)) {
                        //$tmp['leaf'] = true;
                        $tmp['expanded'] = true;
                        $tmp['children'] = [];
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

        $volume = $this->get('phlexible_media_manager.volume_manager')->getByFolderId($parentId);
        $parentFolder = $volume->findFolder($parentId);

        try {
            $folder = $volume->createFolder($parentFolder, $name, array(), $this->getUser()->getId());

            return new ResultResponse(true, 'Folder created.', [
                'folder_id'   => $folder->getId(),
                'folder_name' => $folder->getName()
            ]);
        } catch (AlreadyExistsException $e) {
            return new ResultResponse(false, $e->getMessage(), [
                'folder_name' => 'Folder already exists.'
            ]);
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
        $volumeId = $request->get('site_id');
        $folderId = $request->get('folder_id');
        $folderName = $request->get('folder_name');

        $volume = $this->getVolume($volumeId);
        $folder = $volume->findFolder($folderId);

        $volume->renameFolder($folder, $folderName, $this->getUser()->getId());

        return new ResultResponse(true, 'Folder renamed.', [
            'folder_name' => $folderName
        ]);
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
        $volumeId = $request->get('site_id');
        $folderId = $request->get('folder_id');

        $volume = $this->getVolume($volumeId);
        $folder = $volume->findFolder($folderId);

        if ($folder->isRoot()) {
            return new ResultResponse(false, "Can't delete the root folder.");
        }

        $volume->deleteFolder($folder, $this->getUser()->getId());

        return new ResultResponse(true, 'Folder deleted', ['parent_id' => $folder->getParentId()]);
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
        $volumeId = $request->get('site_id');
        $targetId = $request->get('target_id');
        $sourceId = $request->get('source_id');

        $volume = $this->getVolume($volumeId);
        $folder = $volume->findFolder($sourceId);
        $targetFolder = $volume->findFolder($targetId);

        $volume->moveFolder($folder, $targetFolder, $this->getUser()->getId());

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
        $volumeManager = $this->get('phlexible_media_manager.volume_manager');
        $volume = $volumeManager->getByFolderId($folderId);

        try {
            $folder = $volume->findFolder($folderId);

            $calculator = new SizeCalculator();
            $calculatedSize = $calculator->calculate($volume, $folder);

            $data = [
                'title'       => $folder->getName(),
                'type'        => 'folder',
                'path'        => '/' . $folder->getPath(),
                'size'        => $calculatedSize->getSize(),
                'files'       => $calculatedSize->getNumFiles(),
                'folders'     => $calculatedSize->getNumFolders(),
                'create_time' => $folder->getCreatedAt()->format('U') * 1000,
                'create_user' => $folder->getCreateUserId(),
                'modify_time' => $folder->getModifiedAt()->format('U') * 1000,
                'modify_user' => $folder->getModifyUserId(),
            ];
        } catch (\Exception $e) {
            $data = [];
        }

        return new JsonResponse($data);
    }
}
