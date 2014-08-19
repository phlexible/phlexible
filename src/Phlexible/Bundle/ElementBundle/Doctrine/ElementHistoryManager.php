<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Phlexible\Bundle\ElementBundle\Entity\ElementHistory;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;

/**
 * Element history manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementHistoryManager implements ElementHistoryManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $entryRepository = $this->entityManager->getRepository('PhlexibleElementBundle:ElementHistory');
        $qb = $entryRepository->createQueryBuilder('h');
        $this->applyCriteriaToQueryBuilder($criteria, $qb);

        if ($orderBy) {
            foreach ($orderBy as $field => $dir) {
                $qb->orderBy("h.$field", $dir);
            }
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countBy(array $criteria)
    {
        $entryRepository = $this->entityManager->getRepository('PhlexibleElementBundle:ElementHistory');
        $qb = $entryRepository->createQueryBuilder('h');
        $qb->select('COUNT(h.id)');
        $this->applyCriteriaToQueryBuilder($criteria, $qb);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array        $criteria
     * @param QueryBuilder $qb
     */
    private function applyCriteriaToQueryBuilder(array $criteria, QueryBuilder $qb)
    {
        foreach ($criteria as $key => $value) {
            if (in_array($key, array('treeId', 'teaserId', 'eid', 'version'))) {
                $qb->andWhere($qb->expr()->eq("h.$key", $value));
            } elseif (in_array($key, array('language', 'action'))) {
                $qb->andWhere($qb->expr()->eq("h.$key", $qb->expr()->literal($value)));
            } elseif (in_array($key, array('comment'))) {
                $qb->andWhere($qb->expr()->like("h.$key", $qb->expr()->literal("%$value%")));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function insert($action, $eid, $userId, $treeId = null, $teaserId = null, $version = null, $language = null, $comment = null)
    {
        $entry = new ElementHistory();
        $entry
            ->setEid($eid)
            ->setTeaserId($teaserId)
            ->setTreeId($treeId)
            ->setLanguage($language)
            ->setVersion($version)
            ->setAction($action)
            ->setComment($comment)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($userId);

        $this->entityManager->persist($entry);
        $this->entityManager->flush($entry);

        return $this;
    }
}
