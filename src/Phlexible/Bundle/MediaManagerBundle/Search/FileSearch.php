<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Search;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Bundle\SearchBundle\Search\SearchResult;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Component\Volume\VolumeManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * File search
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileSearch implements SearchProviderInterface
{
    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param VolumeManager                 $volumeManager
     * @param UserManagerInterface          $userManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        VolumeManager $volumeManager,
        UserManagerInterface $userManager,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->volumeManager = $volumeManager;
        $this->userManager = $userManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return 'ROLE_MEDIA';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchKey()
    {
        return 'mm';
    }

    /**
     * {@inheritdoc}
     */
    public function search($query)
    {
        $files = [];
        foreach ($this->volumeManager->all() as $volume) {
            $foundFiles = $volume->search($query);
            if ($foundFiles) {
                $files += $foundFiles;
            }
        }

        $folders = [];

        $results = [];
        foreach ($files as $file) {
            /* @var $file ExtendedFileInterface */

            if (empty($folders[$file->getFolderId()])) {
                $folders[$file->getFolderId()] = $file->getVolume()->findFolder($file->getFolderId());
            }

            if (!$this->authorizationChecker->isGranted($folders[$file->getFolderId()], 'FILE_READ')) {
                continue;
            }

            $folderPath = $folders[$file->getFolderId()]->getIdPath();

            try {
                $createUser = $this->userManager->find($file->getCreateUserId());
            } catch (\Exception $e) {
                $createUser = $this->userManager->getSystemUser();
            }

            $results[] = new SearchResult(
                $file->getId(),
                $file->getName(),
                $createUser->getDisplayname(),
                $file->getCreatedAt()->format('U'),
                '/media/' . $file->getId() . '/_mm_small',
                'Mediamanager File Search',
                [
                    'xtype'      => 'Phlexible.mediamanager.menuhandle.MediaHandle',
                    'parameters' => [
                        'start_file_id'     => $file->getId(),
                        'start_folder_path' => $folderPath
                    ],
                ]
            );
        }

        return $results;
    }
}
