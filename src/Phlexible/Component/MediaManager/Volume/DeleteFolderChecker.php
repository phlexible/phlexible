<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Volume;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\MediaManagerBundle\Entity\FolderUsage;
use Phlexible\Component\Volume\Model\FolderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Delete folder checker.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DeleteFolderChecker
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var string
     */
    private $deletePolicy;

    /**
     * @param EntityManager                 $entityManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param string                        $deletePolicy
     */
    public function __construct(
        EntityManager $entityManager,
        AuthorizationCheckerInterface $authorizationChecker,
        $deletePolicy)
    {
        $this->entityManager = $entityManager;
        $this->authorizationChecker = $authorizationChecker;
        $this->deletePolicy = $deletePolicy;
    }

    /**
     * @param FolderInterface $folder
     *
     * @return bool
     */
    public function isDeleteAllowed(FolderInterface $folder)
    {
        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') && !$this->authorizationChecker->isGranted('FOLDER_DELETE', $folder)) {
            return false;
        }

        if ($this->deletePolicy === 'delete_all') {
            return true;
        }

        $folderUsageRepository = $this->entityManager->getRepository('PhlexibleMediaManagerBundle:FolderUsage');
        $folderUsages = $folderUsageRepository->findBy(['folder' => $folder]);

        foreach ($folderUsages as $folderUsage) {
            if (in_array($folderUsage->getStatus(), [FolderUsage::STATUS_ONLINE, FolderUsage::STATUS_LATEST])) {
                return false;
            }
            if ($folderUsage->getStatus() === FolderUsage::STATUS_OLD && $this->deletePolicy === 'hide_old') {
                return false;
            }
        }

        return true;
    }
}
