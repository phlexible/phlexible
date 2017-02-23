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
 * Title search.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TitleSearch extends AbstractSearch
{
    /**
     * {@inheritdoc}
     */
    public function getSearchKey()
    {
        return 'e';
    }

    /**
     * {@inheritdoc}
     */
    public function search($query)
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $qb
            ->select('t.id', 't.siteroot_id')
            ->from('element_version_mapped_field', 'evmf')
            ->join('evmf', 'element_version', 'ev', 'evmf.element_version_id = ev.id AND evmf.language = '.$qb->expr()->literal($this->getDefaultLanguage()))
            ->join('ev', 'element', 'e', 'ev.eid = e.eid AND ev.version = e.latest_version')
            ->join('e', 'tree', 't', 'e.eid = t.type_id')
            ->where($qb->expr()->like('evmf.backend', $qb->expr()->literal("%$query%")))
            ->andWhere($qb->expr()->eq('evmf.language', $qb->expr()->literal($this->getDefaultLanguage())))
            ->orderBy('evmf.backend');

        $rows = $this->getConnection()->fetchAll($qb->getSQL());

        return parent::doSearch($rows, 'Elements Title Search');
    }
}
