<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Generator.php 2312 2007-01-25 18:46:27Z swentz $
 */

namespace Phlexible\Bundle\TreeBundle;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tree filter
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeFilter
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    /**
     * @var EventDispatcherInterface
     */
    protected $_dispatcher = null;

    /**
     * @var array
     */
    protected $_filterValues = array();

    /**
     * @var string
     */
    protected $_sortMode = 'sort';

    /**
     * @var string
     */
    protected $_sortDir = 'ASC';

    /**
     * @var integer
     */
    protected $_tid = null;

    /**
     * @var string
     */
    protected $_language = null;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_storage = null;

    /**
     * Constructor
     *
     * @param MWF_Db_Pool 			   $dbPool
     * @param EventDispatcherInterface $dispatcher
     * @param integer                  $tid
     * @param string                   $language
     */
    public function __construct(MWF_Db_Pool $dbPool, EventDispatcherInterface $dispatcher, $tid, $language)
    {
        $this->_db = $dbPool->read;
        $this->_dispatcher = $dispatcher;
        $this->_tid = $tid;
        $this->_language = $language;

        $this->_storage = new Zend_Session_Namespace('elements_filter_' . $tid . '_' . $language);

        if (isset($this->_storage->filterValues))
        {
            $this->setFilterValues($this->_storage->filterValues);
        }

        if (isset($this->_storage->sortMode))
        {
            $this->setSortMode($this->_storage->sortMode);
        }

        if (isset($this->_storage->sortDir))
        {
            $this->setSortDir($this->_storage->sortDir);
        }
    }

    public function reset()
    {
        $this->_storage->unsetAll();
    }

    /**
     * Set filter values
     *
     * @param array $filterValues
     * @return Makeweb_Elements_Tree_Filter
     */
    public function setFilterValues(array $filterValues)
    {
        $this->_storage->filterValues = $this->_filterValues = $filterValues;

        return $this;
    }

    /**
     * Return filter values
     *
     * @return array
     */
    public function getFilterValues()
    {
        return $this->_filterValues;
    }

    /**
     * Set sort mode
     *
     * @param string $sortMode
     * @return Makeweb_Elements_Tree_Filter
     */
    public function setSortMode($sortMode)
    {
        $this->_storage->sortMode = $this->_sortMode = $sortMode;

        return $this;
    }

    /**
     * Return sort mode
     *
     * @return string
     */
    public function getSortMode()
    {
        return $this->_sortMode;
    }

    /**
     * Set sort dir
     *
     * @param string $sortDir
     * @return Makeweb_Elements_Tree_Filter
     */
    public function setSortDir($sortDir)
    {
        $this->_storage->sortDir = $this->_sortDir = $sortDir;

        return $this;
    }

    /**
     * Return sort dir
     *
     * @return string
     */
    public function getSortDir()
    {
        return $this->_sortDir;
    }

    public function getPager($id)
    {
        $ids = $this->_fetchAllIds();
        $pos = array_search($id, $ids);

        $pager = array(
            'first'   => $ids[0],
            'prev'    => null,
            'current' => $id,
            'next'    => null,
            'last'    => $ids[count($ids) - 1],
            'pos'     => $pos + 1,
            'total'   => count($ids),
        );

        if ($pos > 0)
        {
            $pager['prev'] = $ids[$pos - 1];
        }

        if ($pos < count($ids) - 1)
        {
            $pager['next'] = $ids[$pos + 1];
        }

        return $pager;
    }

    public function getCount()
    {
        return $this->_fetchCount();
    }

    public function getIds($limit = null, $start = null)
    {
        return $this->_fetchRangeIds($limit, $start);
    }

    protected function _fetchCount()
    {
        $select = $this->_getFilterSelect();
        #$select->columns(array(new Zend_Db_Expr('COUNT(et.id)')));
        $select->columns(array('et.id'));

        #$cnt = $this->_db->fetchOne($select);
        $cnt = count($this->_db->fetchCol($select));

        return $cnt;
    }

    protected function _fetchAllIds()
    {
        $select = $this->_getFilterAndSortSelect();
        $select->columns(array('et.id'));

        $ids = $this->_db->fetchCol($select);

        return $ids;

    }
    protected function _fetchRangeIds($limit, $start)
    {
        $select = $this->_getFilterAndSortSelect();
        $select->columns(array('et.id', 'e.latest_version'));

        if (null !== $limit)
        {
            $select->limit($limit, $start);
        }
        $ids = $this->_db->fetchPairs($select);

        return $ids;
    }

    protected function _getFilterAndSortSelect()
    {
        $select = $this->_getFilterSelect();

        switch ($this->_sortMode)
        {
            case 'title':
                $select
                    ->joinLeft(
                        array('evt_sort' => $this->_db->prefix . 'element_version_titles'),
                        'evt_sort.eid = et.eid AND evt_sort.version = e.latest_version and evt_sort.language = ' . $this->_db->quote($this->_language),
                        array()
                    )
                    ->order('evt_sort.backend ' . $this->_sortDir);
                break;

            case 'create_time':
                $select->order('et.modify_time ' . $this->_sortDir);
                break;

            case 'publish_time':
                $select
                    ->joinLeft(
                        array('eto_sort' => $this->_db->prefix . 'element_tree_online'),
                        'eto_sort.tree_id = et.id AND eto_sort.language = ' . $this->_db->quote($this->_language),
                        array()
                    )
                    ->order('eto_sort.publish_time ' . $this->_sortDir);
                break;

            case 'custom_date':
                $select
                    ->joinLeft(
                        array('evt_sort' => $this->_db->prefix . 'element_version_titles'),
                        'evt_sort.eid = et.eid AND evt_sort.version = e.latest_version and evt_sort.language = ' . $this->_db->quote($this->_language),
                        array()
                    )
                    ->order('evt_sort.date ' . $this->_sortDir);
                break;

            case 'sort':
            default:
                $select->order('et.sort ' . $this->_sortDir);
                break;
        }

        return $select;
    }

    protected function _getFilterSelect()
    {
        $select = $this->_db->select()
            ->distinct()
            ->from(
                array('et' => $this->_db->prefix . 'element_tree'),
                array()
            )
            ->joinLeft(
                    array('e' => $this->_db->prefix . 'element'),
                    'et.eid = e.eid',
                    array()
                )
            ->where('et.parent_id = ?', $this->_tid);

        if (!empty($this->_filterValues['status']))
        {
            $select->joinLeft(
                array('eto_1' => $this->_db->prefix . 'element_tree_online'),
                'eto_1.tree_id = et.id AND eto_1.language = ' . $this->_db->quote($this->_language),
                array()
            );

            $where = '0';

            $filterStatus = explode(',', $this->_filterValues['status']);

            if (in_array('online', $filterStatus))
            {
                $where .= ' OR eto_1.version = e.latest_version';
            }
            if (in_array('async', $filterStatus))
            {
                $select->joinLeft(
                    array('eth_1' => $this->_db->prefix . 'element_tree_hash'),
                    'eth_1.tid = eto_1.tree_id AND eth_1.language = eto_1.language AND eth_1.version = eto_1.version',
                    array()
                );
                $select->joinLeft(
                    array('eth_2' => $this->_db->prefix . 'element_tree_hash'),
                    'eth_2.tid = eto_1.tree_id AND eth_2.language = eto_1.language AND eth_2.version = e.latest_version',
                    array()
                );

                $where .= ' OR (eto_1.version != e.latest_version AND eth_1.hash != eth_2.hash)';
            }
            if (in_array('offline', $filterStatus))
            {
                $where .= ' OR eto_1.version IS NULL';
            }

            $select->where($where);
        }

        if (!empty($this->_filterValues['navigation']) || !empty($this->_filterValues['restricted']))
        {
            $select->joinLeft(
                array('etp_1' => $this->_db->prefix . 'element_tree_page'),
                'etp_1.tree_id = et.id AND etp_1.version = e.latest_version',
                array()
            );

            if (!empty($this->_filterValues['navigation']))
            {
                if ($this->_filterValues['navigation'] == 'in_navigation')
                {
                    $select->where('etp_1.navigation = 1');
                }
                else
                {
                    $select->where('etp_1.navigation = 0');
                }
            }

            if (!empty($this->_filterValues['restricted']))
            {
                if ($this->_filterValues['restricted'] == 'is_restricted')
                {
                    $select->where('etp_1.restricted = 1');
                }
                else
                {
                    $select->where('etp_1.restricted =0');
                }
            }
        }

        if (!empty($this->_filterValues['date']) && (!empty($this->_filterValues['date_from']) || !empty($this->_filterValues['date_to'])))
        {
            switch ($this->_filterValues['date'])
            {
                case 'create':
                    if (!empty($this->_filterValues['date_from']))
                    {
                        $select->where('e.create_time >= ?', $this->_filterValues['date_from']);
                    }
                    if (!empty($this->_filterValues['date_to']))
                    {
                        $select->where('e.create_time <= ?', $this->_filterValues['date_to']);
                    }
                    break;

                case 'publish':
                    $select->joinLeft(
                        array('eto_2' => $this->_db->prefix . 'element_tree_online'),
                        'eto_2.tree_id = et.id AND eto_2.language = ' . $this->_db->quote($this->_language),
                        array()
                    );
                    if (!empty($this->_filterValues['date_from']))
                    {
                        $select->where('eto_2.publish_time >= ?', $this->_filterValues['date_from']);
                    }
                    if (!empty($this->_filterValues['date_to']))
                    {
                        $select->where('eto_2.publish_time <= ?', $this->_filterValues['date_to']);
                    }
                    break;

                case 'custom':
                    $select->joinLeft(
                        array('evt_1' => $this->_db->prefix . 'element_version_titles'),
                        'evt_1.eid = e.eid AND evt_1.version = e.latest_version AND evt_1.language = ' . $this->_db->quote($this->_language),
                        array()
                    );
                    if (!empty($this->_filterValues['date_from']))
                    {
                        $select->where('evt_1.date >= ?', $this->_filterValues['date_from']);
                    }
                    if (!empty($this->_filterValues['date_to']))
                    {
                        $select->where('evt_1.date <= ?', $this->_filterValues['date_to']);
                    }
                    break;
            }
        }

        if (!empty($this->_filterValues['search']))
        {
            $select->join(
                array('edl_1' => $this->_db->prefix . 'element_data_language'),
                'edl_1.eid = e.eid AND edl_1.version = e.latest_version AND edl_1.language = ' . $this->_db->quote($this->_language) . ' AND edl_1.content LIKE ' . $this->_db->quote('%' . $this->_filterValues['search'] . '%'),
                array()
            );
            $select->group(array('et.id', 'e.latest_version'));
        }

        $event = new Makeweb_Elements_Event_ListFilter($this->_filterValues, $select);
        $this->_dispatcher->dispatch($event);

        return $select;
    }
}
