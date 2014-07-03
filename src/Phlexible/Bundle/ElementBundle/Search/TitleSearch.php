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
 * @author  Stephan Wentz <sw@brainbits.net>
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
        $select = $this->db->select()
            ->from(
                array('evt' => $this->db->prefix . 'element_version_titles'),
                array()
            )
            ->join(
                array('e' => $this->db->prefix . 'element'),
                'evt.eid = e.eid AND evt.version = e.latest_version AND evt.language = ' . $this->db->quote(
                    $this->defaultLanguage
                ),
                array()
            )
            ->join(
                array('et' => $this->db->prefix . 'element_tree'),
                'evt.eid = et.eid',
                array('id', 'siteroot_id')
            )
            ->where('evt.backend LIKE ?', '%' . $query . '%')
            ->where('evt.language = ?', $this->defaultLanguage)
            ->order('evt.backend ASC');

        return parent::_doSearch($select, 'Elements Title Search');
    }
}