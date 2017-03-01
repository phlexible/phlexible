<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\EventDispatcher\Event;

/**
 * Tree filter event.
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
