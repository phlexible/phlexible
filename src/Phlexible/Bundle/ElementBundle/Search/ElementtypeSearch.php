<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Search;

/**
 * Elementtype search
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeSearch extends AbstractSearch
{
    /**
     * {@inheritdoc}
     */
    public function getSearchKey()
    {
        return 'et';
    }

    /**
     * {@inheritdoc}
     */
    public function search($query)
    {
        $select = $this->db->select()
            ->distinct()
            ->from(
                array('etv' => $this->db->prefix.'elementtype_version'),
                array(
                    'et.id',
                    'et.siteroot_id'
                )
            )
            ->join(
                array('ev' => $this->db->prefix.'element_version'),
                'ev.element_type_id = etv.element_type_id AND ev.element_type_version = etv.version',
                array()
            )
            ->join(
                array('et' => $this->db->prefix.'element_tree'),
                'ev.eid = et.eid',
                array()
            )
            ->where('etv.title LIKE ?', '%'.$query.'%')
            ->order('etv.title ASC');

        return parent::_doSearch($select, 'Elements Elementtype Search');
    }
}
