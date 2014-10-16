<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Search;

/**
 * Title search
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
            ->join('evmf', 'element', 'e', 'evmf.eid = e.eid AND evmf.version = e.latest_version AND evmf.language = ' . $qb->expr()->literal($this->getDefaultLanguage()))
            ->join('evmf', 'tree', 't', 'evmf.eid = t.eid')
            ->where($qb->expr()->like('evmf.backend', $qb->expr()->literal("%query%")))
            ->andWhere('evmf.language = ?', $this->getDefaultLanguage())
            ->orderBy('evmf.backend ASC');

        $rows = $this->getConnection()->fetchAll($qb->getSQL());

        return parent::doSearch($rows, 'Elements Title Search');
    }
}