<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Search;

/**
 * TID search.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TidSearch extends AbstractSearch
{
    /**
     * {@inheritdoc}
     */
    public function getSearchKey()
    {
        return 'tid';
    }

    /**
     * {@inheritdoc}
     */
    public function search($query)
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $qb
            ->select('t.id')
            ->from('tree', 't')
            ->join('t', 'element', 'e', 'e.eid = t.type_id')
            ->where($qb->expr()->eq('t.id', $qb->expr()->literal($query)));

        $rows = $this->getConnection()->fetchAll($qb->getSQL());

        return parent::doSearch($rows, 'Elements TID Search');
    }
}
