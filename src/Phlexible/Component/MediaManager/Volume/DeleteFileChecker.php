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
use Phlexible\Bundle\MediaManagerBundle\Entity\FileUsage;
use Phlexible\Component\Volume\Model\FileInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Delete file checker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DeleteFileChecker
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
     * @param FileInterface $file
     *
     * @return bool
     */
    public function isDeleteAllowed(FileInterface $file)
    {
        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') && !$this->authorizationChecker->isGranted('FILE_DELETE', $file->getVolume()->findFolder($file->getFolderId()))) {
            return false;
        }

        if ($this->deletePolicy === 'delete_all') {
            return true;
        }

        $fileUsageRepository = $this->entityManager->getRepository('PhlexibleMediaManagerBundle:FileUsage');
        $fileUsages = $fileUsageRepository->findBy(['file' => $file]);

        foreach ($fileUsages as $fileUsage) {
            if (in_array($fileUsage->getStatus(), [FileUsage::STATUS_ONLINE, FileUsage::STATUS_LATEST])) {
                return false;
            }
            if ($fileUsage->getStatus() === FileUsage::STATUS_OLD && $this->deletePolicy === 'hide_old') {
                return false;
            }
        }

        return true;
    }
}
