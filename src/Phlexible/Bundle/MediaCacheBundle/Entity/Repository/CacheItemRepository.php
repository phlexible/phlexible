<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Cache item repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheItemRepository extends EntityRepository
{
    /**
     * @param array $criteria
     *
     * @return int
     */
    public function countBy(array $criteria)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)');

        foreach ($criteria as $key => $value) {
            $qb->where($qb->expr()->eq("c.$key", $qb->expr()->literal($value)));
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function countAll()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
