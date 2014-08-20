<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Search;

/**
 * Unique ID search
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class UniqueIdSearch extends AbstractSearch
{
    /**
     * {@inheritdoc}
     */
    public function getSearchKey()
    {
        return 'eu';
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
            ->join('t', 'element', 'e', 'e.eid = et.eid')
            ->where($qb->expr()->like('e.unique', $qb->expr()->literal("%$query%")));

        $rows = $this->getConnection()->fetchAll($qb->getSQL());

        return parent::doSearch($rows, 'Elements Unique ID Search');
    }
}
