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
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;

/**
 * File usage manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileUsageManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $fileUsageRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->fileUsageRepository = $entityManager->getRepository('PhlexibleMediaManagerBundle:FileUsage');
    }

    /**
     * Return aggregated status
     *
     * @param ExtendedFileInterface $file
     *
     * @return int
     */
    public function getStatus(ExtendedFileInterface $file)
    {
        $status = 0;
        foreach ($this->findStatusByFile($file) as $status) {
            $status &= $status;
        }

        return $status;
    }

    /**
     * Return highest aggregated status
     *
     * @param ExtendedFileInterface $file
     *
     * @return int
     */
    public function getHighestStatus(ExtendedFileInterface $file)
    {
        $status = $this->getStatus($file);

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
     * @param ExtendedFileInterface $file
     *
     * @return array
     */
    public function getUsedIn(ExtendedFileInterface $file)
    {
        $qb = $this->fileUsageRepository->createQueryBuilder('u');
        $qb
            ->select(['u.usageType', 'u.usageId', 'u.status'])
            ->join('u.file', 'f')
            ->where($qb->expr()->eq('f.id', $qb->expr()->literal($file->getId())))
            ->andWhere($qb->expr()->eq('f.version', $qb->expr()->literal($file->getVersion())));

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
     * @param ExtendedFileInterface $file
     *
     * @return array
     */
    private function findStatusByFile(ExtendedFileInterface $file)
    {
        $qb = $this->fileUsageRepository->createQueryBuilder('u');
        $qb
            ->select('u.status')
            ->join('u.file', 'f')
            ->where($qb->expr()->eq('f.id', $qb->expr()->literal($file->getId())))
            ->andWhere($qb->expr()->eq('f.version', $qb->expr()->literal($file->getVersion())));

        return array_column($qb->getQuery()->getScalarResult(), 'status');
    }
}
