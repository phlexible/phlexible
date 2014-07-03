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
        $select = $this->db->select()
            ->from(array('et' => $this->db->prefix . 'element_tree'), array('id'))
            ->join(array('e' => $this->db->prefix . 'element'), 'e.eid = et.eid', array())
            ->where('et.id = ?', $query);

        return parent::_doSearch($select, 'Elements TID Search');
    }
}
