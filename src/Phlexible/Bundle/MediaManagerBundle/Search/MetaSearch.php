<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Search;

use Phlexible\Bundle\MediaManagerBundle\Meta\FileMetaDataManager;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Bundle\SearchBundle\Search\SearchResult;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Component\Volume\VolumeManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Meta search
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSearch implements SearchProviderInterface
{
    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @var FileMetaDataManager
     */
    private $metaDataManager;

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
     * @param FileMetaDataManager           $metaDataManager
     * @param UserManagerInterface          $userManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        VolumeManager $volumeManager,
        FileMetaDataManager $metaDataManager,
        UserManagerInterface $userManager,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->volumeManager = $volumeManager;
        $this->metaDataManager = $metaDataManager;
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

        foreach ($this->metaDataManager->findByValue($query) as $metaData) {
            $identifiers = $metaData->getIdentifiers();
            $file = $volume->findFile($identifiers['file_id']);
            $files[$file->getId()] = $file;
        }

        $folders = [];
        $results = [];
        foreach ($files as $file) {
            /* @var $file ExtendedFileInterface */

            if (empty($folders[$file->getFolderId()])) {
                $folders[$file->getFolderId()] = $file->getVolume()->findFolder($file->getFolderId());
            }

            if (!$this->authorizationChecker->isGranted('FILE_READ', $folders[$file->getFolderId()])) {
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
                'Mediamanager Meta Search',
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
