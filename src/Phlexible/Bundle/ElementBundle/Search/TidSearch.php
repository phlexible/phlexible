<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Search;

/**
 * TID search
 *
 * @author  Stephan Wentz <sw@brainbits.net>
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
            ->join('t', 'element', 'e', 'e.eid = t.eid')
            ->where($qb->expr()->eq('t.id', $qb->expr()->literal($query)));

        $rows = $this->getConnection()->fetchAll($qb->getSQL());

        return parent::doSearch($rows, 'Elements TID Search');
    }
}
