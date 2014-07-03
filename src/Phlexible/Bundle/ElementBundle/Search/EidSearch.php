<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Search;

/**
 * EID search
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class EidSearch extends AbstractSearch
{
    /**
     * {@inheritdoc}
     */
    public function getSearchKey()
    {
        return 'eid';
    }

    /**
     * {@inheritdoc}
     */
    public function search($query)
    {
        $select = $this->db->select()
            ->from(
                array('e' => $this->db->prefix . 'element'),
                array()
            )
            ->join(
                array('et' => $this->db->prefix . 'element_tree'),
                'e.eid = et.eid',
                array('id')
            )
            ->where('e.eid = ?', $query);

        return parent::_doSearch($select, 'Elements EID Search');
    }
}
