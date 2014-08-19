<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\Entity\ElementLock;

/**
 * Element lock repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementLockRepository extends EntityRepository
{
    /**
     * Retrieve lock by identifier
     *
     * @param int    $eid
     * @param string $userId
     *
     * @return ElementLock
     */
    public function findByEidAndUserId($eid, $userId)
    {
        return $this->findBy(array('eid' => $eid, 'userId' => $userId));
    }

    /**
     * Retrieve lock by identifier
     *
     * @param int    $eid
     * @param string $notUserId
     *
     * @return ElementLock
     */
    public function findByEidAndNotUserId($eid, $notUserId)
    {
        $qb = $this->createQueryBuilder('l');
        $qb
            ->where($qb->expr()->eq('l.eid', $eid))
            ->andWhere($qb->expr()->neq('l.userId', $qb->expr()->literal($notUserId)));

        return $qb->getQuery()->getResult();
    }

    /**
     * Retrieve lock by identifier
     *
     * @param int $eid
     *
     * @return ElementLock[]
     */
    public function findByEid($eid)
    {
        return $this->findBy(array('eid' => $eid));
    }
}