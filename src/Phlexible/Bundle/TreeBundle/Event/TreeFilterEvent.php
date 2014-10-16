<?php

namespace Phlexible\Bundle\TreeBundle\Event;

use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\EventDispatcher\Event;

/**
 * Tree filter event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeFilterEvent extends Event
{
    /**
     * @var array
     */
    private $filterData;

    /**
     * @var QueryBuilder
     */
    private $qb;

    /**
     * @param array        $filterData
     * @param QueryBuilder $qb
     */
    public function __construct(array $filterData, QueryBuilder $qb)
    {
        $this->filterData = $filterData;
        $this->qb = $qb;
    }

    /**
     * @return array
     */
    public function getFilterData()
    {
        return $this->filterData;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->qb;
    }
}
