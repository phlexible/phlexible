<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Composite;
use Doctrine\ORM\QueryBuilder;
use Phlexible\Bundle\MessageBundle\Criteria\Criteria;
use Phlexible\Bundle\MessageBundle\Criteria\Criterium;
use Phlexible\Bundle\MessageBundle\Entity\Filter;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Message repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageRepository extends EntityRepository
{
    /**
     * @return Message
     */
    public function create()
    {
        return new Message();
    }

    /**
     * Find messages by criteria
     *
     * @param Criteria $criteria
     * @param array    $order
     * @param int      $limit
     * @param int      $offset
     *
     * @return Filter
     */
    public function findByCriteria(Criteria $criteria, $order = null, $limit = null, $offset = 0)
    {
        $qb = $this->createCriteriaQueryBuilder($criteria, 'm');

        if ($order) {
            foreach ($order as $field => $dir) {
                $qb->orderBy("m.$field", $dir);
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
     * @param Criteria $criteria
     *
     * @return int
     */
    public function countByCriteria(Criteria $criteria)
    {
        $qb = $this->createCriteriaQueryBuilder($criteria, 'm');

        $qb->select('COUNT(m.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param Filter $filter
     * @param int    $limit
     * @param int    $offset
     * @param string $order
     *
     * @return Message[]
     */
    public function findByFilter(Filter $filter, $limit = null, $offset = 0, $order = null)
    {
        return $this->findByCriteria($filter->getCriteria(), $limit, $offset, $order);
    }

    /**
     * @param Filter $filter
     *
     * @return int
     */
    public function countByFilter(Filter $filter)
    {
        return $this->countByCriteria($filter->getCriteria());
    }

    /**
     * @return array
     */
    public function getFacets()
    {
        $channels = $this->createQueryBuilder('m')->select('DISTINCT m.channel')->getQuery()->getScalarResult();
        $types = $this->createQueryBuilder('m')->select('DISTINCT m.type')->getQuery()->getScalarResult();
        $priorities = $this->createQueryBuilder('m')->select('DISTINCT m.priority')->getQuery()->getScalarResult();
        $resources = $this->createQueryBuilder('m')->select('DISTINCT m.resource')->getQuery()->getScalarResult();

        return array(
            'channels'   => array_column($channels, 'channel'),
            'types'      => array_column($types, 'type'),
            'priorities' => array_column($priorities, 'priority'),
            'resources'  => array_column($resources, 'resource'),
        );
    }

    /**
     * @param Criteria $criteria
     *
     * @return array
     */
    public function getFacetsByCriteria(Criteria $criteria)
    {
        $qb = $this->createCriteriaQueryBuilder($criteria, 'm');

        $channelsQb = clone $qb;
        $channels = $channelsQb->select('DISTINCT m.channel')->getQuery()->getScalarResult();

        $typeQb = clone $qb;
        $types = $typeQb->select('DISTINCT m.type')->getQuery()->getScalarResult();

        $priorityQb = clone $qb;
        $priorities = $priorityQb->select('DISTINCT m.priority')->getQuery()->getScalarResult();

        $resourceQb = clone $qb;
        $resources = $resourceQb->select('DISTINCT m.resource')->getQuery()->getScalarResult();

        return array(
            'channels'   => array_column($channels, 'channel'),
            'types'      => array_column($types, 'type'),
            'priorities' => array_column($priorities, 'priority'),
            'resources'  => array_column($resources, 'resource'),
        );
    }

    /**
     * @param Criteria $criteria
     * @param string   $prefix
     *
     * @return QueryBuilder
     */
    private function createCriteriaQueryBuilder(Criteria $criteria, $prefix)
    {
        $qb = $this->createQueryBuilder($prefix);

        if ($criteria->count()) {
            if ($criteria->getMode() === Criteria::MODE_OR) {
                $composite = $qb->expr()->orX();
            } else {
                $composite = $qb->expr()->andX();
            }

            $this->applyCriteriaToQueryBuilder($criteria, $qb, $composite, $prefix);

            if ($composite->count()) {
                $qb->where($composite);
            }
        }

        return $qb;
    }

    /**
     * Apply criteria to select
     *
     * @param Criteria     $criteria
     * @param QueryBuilder $qb
     * @param Composite    $composite
     * @param string       $prefix
     */
    private function applyCriteriaToQueryBuilder(Criteria $criteria, QueryBuilder $qb, Composite $composite, $prefix)
    {
        foreach ($criteria as $criterium) {
            if ($criteria->getMode() === Criteria::MODE_OR) {
                $expr = $qb->expr()->orX();
            } else {
                $expr = $qb->expr()->andX();
            }
            if ($criterium instanceof Criteria) {
                $this->applyCriteriaToQueryBuilder($criterium, $qb, $expr, $prefix);
            } else {
                $this->applyCriteriumToQueryBuilder($criterium, $qb, $expr, $prefix);
            }

            $composite->add($expr);
        }
    }

    /**
     * @param Criterium    $criterium
     * @param QueryBuilder $qb
     * @param Composite    $composite
     * @param string       $prefix
     */
    private function applyCriteriumToQueryBuilder(Criterium $criterium, QueryBuilder $qb, Composite $composite, $prefix)
    {
        $type = $criterium->getType();
        $value = $criterium->getValue();

        if (is_string($value) && !strlen($value)) {
            return;
        }

        switch ($type) {
            case Criteria::CRITERIUM_SUBJECT_LIKE:
                $composite->add($qb->expr()->like("$prefix.subject", $qb->expr()->literal("%value%")));
                break;

            case Criteria::CRITERIUM_SUBJECT_NOT_LIKE:
                $composite->add($qb->expr()->notLike("$prefix.subject", $qb->expr()->literal("%value%")));
                break;

            case Criteria::CRITERIUM_BODY_LIKE:
                $composite->add($qb->expr()->like("$prefix.body", $qb->expr()->literal("%value%")));
                break;

            case Criteria::CRITERIUM_BODY_NOT_LIKE:
                $composite->add($qb->expr()->notLike("$prefix.body", $qb->expr()->literal("%value%")));
                break;

            case Criteria::CRITERIUM_PRIORITY_IS:
                $composite->add($qb->expr()->eq("$prefix.priority", $value));
                break;

            case Criteria::CRITERIUM_PRIORITY_MIN:
                $composite->add($qb->expr()->gte("$prefix.priority", $value));
                break;

            case Criteria::CRITERIUM_PRIORITY_IN:
                $composite->add($qb->expr()->in("$prefix.priority", $value));
                break;

            case Criteria::CRITERIUM_TYPE_IS:
                $composite->add($qb->expr()->eq("$prefix.type", $value));
                break;

            case Criteria::CRITERIUM_TYPE_IN:
                $composite->add($qb->expr()->in("$prefix.type", $value));
                break;

            case Criteria::CRITERIUM_CHANNEL_IS:
                $composite->add($qb->expr()->eq("$prefix.channel", $qb->expr()->literal("value")));
                break;

            case Criteria::CRITERIUM_CHANNEL_LIKE:
                $composite->add($qb->expr()->like("$prefix.channel", $qb->expr()->literal("%value%")));
                break;

            case Criteria::CRITERIUM_CHANNEL_IN:
                $value = (array) $value;
                $values = array_map(
                    function ($value) use ($qb) {
                        return $qb->expr()->literal($value);
                    },
                    $value
                );
                $composite->add($qb->expr()->in("$prefix.channel", $values));
                break;

            case Criteria::CRITERIUM_RESOURCE_IS:
                $composite->add($qb->expr()->eq("$prefix.resource", $qb->expr()->literal("value")));
                break;

            case Criteria::CRITERIUM_RESOURCE_IN:
                $value = (array) $value;
                $values = array_map(
                    function ($value) use ($qb) {
                        return $qb->expr()->literal($value);
                    },
                    $value
                );
                $composite->add($qb->expr()->in("$prefix.resource", $values));
                break;

            case Criteria::CRITERIUM_MAX_AGE:
                $composite->add(
                    $qb->expr()->gte(
                        "$prefix.createdAt",
                        $qb->expr()->literal(date('Y-m-d H:i:s', time() - ($value * 24 * 60 * 60)))
                    )
                );
                break;

            case Criteria::CRITERIUM_MIN_AGE:
                $composite->add(
                    $qb->expr()->lte(
                        "$prefix.createdAt",
                        $qb->expr()->literal(date('Y-m-d H:i:s', time() - ($value * 24 * 60 * 60)))
                    )
                );
                break;

            case Criteria::CRITERIUM_START_DATE:
                $composite->add(
                    $qb->expr()->gte("$prefix.createdAt", $qb->expr()->literal($value->format('Y-m-d H:i:s')))
                );
                break;

            case Criteria::CRITERIUM_END_DATE:
                $composite->add(
                    $qb->expr()->lte("$prefix.createdAt", $qb->expr()->literal($value->format('Y-m-d H:i:s')))
                );
                break;

            case Criteria::CRITERIUM_DATE_IS:
                $composite->add(
                    $qb->expr()->andX(
                        $qb->expr()->gte("$prefix.createdAt", $qb->expr()->literal($value->format('Y-m-d H:i:s'))),
                        $qb->expr()->lt("$prefix.createdAt", $qb->expr()->literal($value->format('Y-m-d H:i:s')))
                    )
                );
                break;
        }
    }
}