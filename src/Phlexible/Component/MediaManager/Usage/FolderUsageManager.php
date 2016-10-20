<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Usage;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;

/**
 * Folder usage manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderUsageManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $folderUsageRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->folderUsageRepository = $entityManager->getRepository('PhlexibleMediaManagerBundle:FolderUsage');
    }

    /**
     * Return aggregated status
     *
     * @param ExtendedFolderInterface $folder
     *
     * @return int
     */
    public function getStatus(ExtendedFolderInterface $folder)
    {
        $status = 0;
        foreach ($this->findStatusByFolder($folder) as $status) {
            $status &= $status;
        }

        return $status;
    }

    /**
     * Return highest aggregated status
     *
     * @param ExtendedFolderInterface $folder
     *
     * @return int
     */
    public function getHighestStatus(ExtendedFolderInterface $folder)
    {
        $status = $this->getStatus($folder);

        if ($status & 8) {
            return 8;
        } elseif ($status & 4) {
            return 4;
        } elseif ($status & 2) {
            return 2;
        } elseif ($status & 1) {
            return 1;
        }

        return 0;
    }

    /**
     * Return aggregated status
     *
     * @param ExtendedFolderInterface $folder
     *
     * @return array
     */
    public function getUsedIn(ExtendedFolderInterface $folder)
    {
        $qb = $this->folderUsageRepository->createQueryBuilder('u');
        $qb
            ->select(['u.usageType', 'u.usageId', 'u.status'])
            ->join('u.folder', 'f')
            ->where($qb->expr()->eq('f.id', $qb->expr()->literal($folder->getId())));

        $usedIn = [];
        foreach ($qb->getQuery()->getScalarResult() as $row) {
            $usedIn[] = [
                'usage_type' => $row['usageType'],
                'usage_id'   => $row['usageId'],
                'status'     => $row['status'],
            ];
        }

        return $usedIn;
    }

    /**
     * @param ExtendedFolderInterface $folder
     *
     * @return array
     */
    private function findStatusByFolder(ExtendedFolderInterface $folder)
    {
        $qb = $this->folderUsageRepository->createQueryBuilder('u');
        $qb
            ->select('u.status')
            ->join('u.folder', 'f')
            ->where($qb->expr()->eq('f.id', $qb->expr()->literal($folder->getId())));

        return array_column($qb->getQuery()->getScalarResult(), 'status');
    }
}
