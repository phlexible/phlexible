<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\QueueBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\QueueBundle\Entity\Job;

/**
 * Queue item repository.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class JobRepository extends EntityRepository
{
    /**
     * @return Job
     */
    public function findNext()
    {
        $date = new \DateTime();
        $qb = $this->createQueryBuilder('q');
        $qb->where($qb->expr()->eq('q.status', Job::STATE_PENDING));
        $qb->andWhere($qb->expr()->lte('q.executeAt', $qb->expr()->literal($date->format('Y-m-d H:i:s'))));
        $qb->orderBy('q.priority', 'DESC');
        $qb->orderBy('q.executeAt', 'ASC');
        $qb->setMaxResults(1);

        $result = $qb->getQuery()->getResult();
        if (!count($result)) {
            return null;
        }

        return current($result);
    }

    /**
     * @param int $status
     *
     * @return int
     */
    public function countByStatus($status)
    {
        $qb = $this->createQueryBuilder('q');
        $qb->where($qb->expr()->eq('q.status', $qb->expr()->literal($status)));
        $qb->select('COUNT(q.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Return job statistics.
     *
     * @param int $status
     *
     * @return array
     */
    public function getStatistics($status)
    {
        $qb = $this->createQueryBuilder('q');
        $qb->select('SUBSTRING(q.job, 1, 200) AS class');
        $qb->where($qb->expr()->eq('q.status', $qb->expr()->literal($status)));

        $result = $qb->getQuery()->getScalarResult();

        $stats = [];
        foreach ($result as $item) {
            if (!preg_match('/"(.+?)"/', $item['class'], $match)) {
                continue;
            }
            $class = $match[1];
            if (!isset($stats[$class])) {
                $stats[$class] = 0;
            }
            ++$stats[$class];
        }

        return $stats;
    }
}
