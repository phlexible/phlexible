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
        $select = $this->db->select()
            ->from(
                array('et' => $this->db->prefix.'element_tree'),
                array('id')
            )
            ->join(
                array('e' => $this->db->prefix.'element'),
                'e.eid = et.eid AND e.unique_id LIKE ' . $this->db->quote('%' . $query . '%'),
                array()
            );

        return parent::_doSearch($select, 'Elements Unique ID Search');
    }
}
