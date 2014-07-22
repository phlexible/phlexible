<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Usage;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;

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
     * @param FolderInterface $folder
     *
     * @return int
     */
    public function getStatus(FolderInterface $folder)
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
     * @param FolderInterface $folder
     *
     * @return int
     */
    public function getHighestStatus(FolderInterface $folder)
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
     * @param FolderInterface $folder
     *
     * @return array
     */
    public function getUsedIn(FolderInterface $folder)
    {
        $qb = $this->folderUsageRepository->createQueryBuilder('u');
        $qb
            ->select(array('u.usageType', 'u.usageId', 'u.status'))
            ->join('u.folder', 'f')
            ->where($qb->expr()->eq('f.id', $qb->expr()->literal($folder->getId())));

        $usedIn = array();
        foreach ($qb->getQuery()->getScalarResult() as $row) {
            $usedIn[] = array(
                'usage_type' => $row['usageType'],
                'usage_id'   => $row['usageId'],
                'status'     => $row['status'],
            );
        }

        return $usedIn;
    }

    /**
     * @param FolderInterface $folder
     *
     * @return array
     */
    private function findStatusByFolder(FolderInterface $folder)
    {
        $qb = $this->folderUsageRepository->createQueryBuilder('u');
        $qb
            ->select('u.status')
            ->join('u.folder', 'f')
            ->where($qb->expr()->eq('f.id', $qb->expr()->literal($folder->getId())));

        return array_column($qb->getQuery()->getScalarResult(), 'status');
    }
}