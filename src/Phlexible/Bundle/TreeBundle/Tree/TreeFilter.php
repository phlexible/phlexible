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

namespace Phlexible\Bundle\TreeBundle\Tree;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Phlexible\Bundle\TreeBundle\Event\TreeFilterEvent;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

/**
 * Tree filter
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeFilter
{
    /**
     * @var Connection
     */
    protected $connection = null;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher = null;

    /**
     * @var array
     */
    private $filterValues = array();

    /**
     * @var string
     */
    private $sortMode = 'sort';

    /**
     * @var string
     */
    private $sortDir = 'ASC';

    /**
     * @var int
     */
    private $tid;

    /**
     * @var string
     */
    private $language;

    /**
     * @var SessionBagInterface
     */
    private $storage = null;

    /**
     * @param Connection               $connection
     * @param Session                  $session
     * @param EventDispatcherInterface $dispatcher
     * @param int                      $tid
     * @param string                   $language
     */
    public function __construct(Connection $connection, Session $session, EventDispatcherInterface $dispatcher, $tid, $language)
    {
        $this->connection = $connection;
        $this->dispatcher = $dispatcher;
        $this->tid = $tid;
        $this->language = $language;

        $bag = new AttributeBag('elements_filter_' . $tid . '_' . $language);
        $session->registerBag($bag);
        $this->storage = $bag;

        if ($this->storage->has('filterValues')) {
            $this->setFilterValues($this->storage->get('filterValues'));
        }

        if ($this->storage->has('sortMode')) {
            $this->setSortMode($this->storage->get('sortMode'));
        }

        if ($this->storage->has('sortDir')) {
            $this->setSortDir($this->storage->get('sortDir'));
        }
    }

    public function reset()
    {
        $this->storage->clear();
    }

    /**
     * Set filter values
     *
     * @param array $filterValues
     *
     * @return $this
     */
    public function setFilterValues(array $filterValues)
    {
        $this->filterValues = $filterValues;
        $this->storage->set('filterValues', $filterValues);

        return $this;
    }

    /**
     * Return filter values
     *
     * @return array
     */
    public function getFilterValues()
    {
        return $this->filterValues;
    }

    /**
     * Set sort mode
     *
     * @param string $sortMode
     *
     * @return $this
     */
    public function setSortMode($sortMode)
    {
        $this->sortMode = $sortMode;
        $this->storage->set('sortMode', $sortMode);

        return $this;
    }

    /**
     * Return sort mode
     *
     * @return string
     */
    public function getSortMode()
    {
        return $this->sortMode;
    }

    /**
     * Set sort dir
     *
     * @param string $sortDir
     *
     * @return $this
     */
    public function setSortDir($sortDir)
    {
        $this->sortDir = $sortDir;
        $this->storage->set('sortDir', $sortDir);

        return $this;
    }

    /**
     * Return sort dir
     *
     * @return string
     */
    public function getSortDir()
    {
        return $this->sortDir;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getPager($id)
    {
        $ids = $this->fetchAllIds();
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

        if ($pos > 0) {
            $pager['prev'] = $ids[$pos - 1];
        }

        if ($pos < count($ids) - 1) {
            $pager['next'] = $ids[$pos + 1];
        }

        return $pager;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->_fetchCount();
    }

    /**
     * @param int $limit
     * @param int $start
     *
     * @return array
     */
    public function getIds($limit = null, $start = null)
    {
        return $this->fetchRangeIds($limit, $start);
    }

    /**
     * @return int
     */
    private function _fetchCount()
    {
        $qb = $this->createFilterQueryBuilder();
        //$select->columns(array(new Zend_Db_Expr('COUNT(et.id)')));
        $qb->select(array('et.id'));

        //$cnt = $this->_db->fetchOne($select);
        $cnt = count($this->connection->fetchAll($qb->getSQL()));

        return $cnt;
    }

    /**
     * @return array
     */
    private function fetchAllIds()
    {
        $qb = $this->createFilterAndSortQueryBuilder();
        $qb->select(array('et.id'));

        $rows = $this->connection->fetchAll($qb->getSQL());

        $ids = array();
        foreach ($rows as $row) {
            $ids[] = $row['id'];
        }

        return $ids;
    }

    /**
     * @param int $limit
     * @param int $start
     *
     * @return array
     */
    private function fetchRangeIds($limit, $start)
    {
        $qb = $this->createFilterAndSortQueryBuilder();
        $qb->select(array('et.id', 'e.latest_version'));

        if (null !== $limit) {
            $qb->setFirstResult($start);
            $qb->setMaxResults($limit);
        }

        $rows = $this->connection->fetchAll($qb->getSQL());

        $ids = array();
        foreach ($rows as $row) {
            $ids[$row['id']] = $row['latest_version'];
        }

        return $ids;
    }

    /**
     * @return QueryBuilder
     */
    private function createFilterAndSortQueryBuilder()
    {
        $qb = $this->createFilterQueryBuilder();

        switch ($this->sortMode) {
            case 'title':
                $qb
                    ->leftJoin('et', 'element_version_titles', 'evt_sort', 'evt_sort.eid = et.eid AND evt_sort.version = e.latest_version and evt_sort.language = ' . $qb->expr()->literal($this->language))
                    ->orderBy('evt_sort.backend', $this->sortDir);
                break;

            case 'create_time':
                $qb->orderBy('et.modify_time', $this->sortDir);
                break;

            case 'publish_time':
                $qb
                    ->leftJoin('et', 'tree_online', 'eto_sort', 'eto_sort.tree_id = et.id AND eto_sort.language = ' . $qb->expr()->literal($this->language))
                    ->orderBy('eto_sort.publish_time', $this->sortDir);
                break;

            case 'custom_date':
                $qb
                    ->leftJoin('et', 'element_version_titles', 'evt_sort', 'evt_sort.eid = et.eid AND evt_sort.version = e.latest_version and evt_sort.language = ' . $qb->expr()->literal($this->language))
                    ->orderBy('evt_sort.date', $this->sortDir);
                break;

            case 'sort':
            default:
                $qb->orderBy('et.sort', $this->sortDir);
                break;
        }

        return $qb;
    }

    private function createFilterQueryBuilder()
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->from('tree', 'et')
            ->leftJoin('et', 'element', 'e', 'et.type_id = e.eid')
            ->where($qb->expr()->eq('et.parent_id', $this->tid));

        if (!empty($this->filterValues['status'])) {
            $qb->leftJoin('et', 'tree_online', 'eto1', 'eto_1.tree_id = et.id AND eto_1.language = ' . $qb->expr()->literal($this->language));

            $where = '0';

            $filterStatus = explode(',', $this->filterValues['status']);

            if (in_array('online', $filterStatus)) {
                $where .= ' OR eto_1.version = e.latest_version';
            }
            if (in_array('async', $filterStatus)) {
                $qb->leftJoin('eto_1', 'tree_hash', 'eth_1', 'eth_1.tid = eto_1.tree_id AND eth_1.language = eto_1.language AND eth_1.version = eto_1.version');
                $qb->leftJoin('eto_1', 'tree_hash', 'eth_2', 'eth_2.tid = eto_1.tree_id AND eth_2.language = eto_1.language AND eth_2.version = e.latest_version');

                $where .= ' OR (eto_1.version != e.latest_version AND eth_1.hash != eth_2.hash)';
            }
            if (in_array('offline', $filterStatus)) {
                $where .= ' OR eto_1.version IS NULL';
            }

            $qb->andWhere($where);
        }

        if (!empty($this->filterValues['navigation']) || !empty($this->filterValues['restricted'])) {
            $qb->leftJoin('et', 'tree_page', 'etp_1', 'etp_1.tree_id = et.id AND etp_1.version = e.latest_version');

            if (!empty($this->filterValues['navigation'])) {
                if ($this->filterValues['navigation'] == 'in_navigation') {
                    $qb->andWhere('etp_1.navigation = 1');
                } else {
                    $qb->andWhere('etp_1.navigation = 0');
                }
            }

            if (!empty($this->filterValues['restricted'])) {
                if ($this->filterValues['restricted'] == 'is_restricted') {
                    $qb->andWhere('etp_1.restricted = 1');
                } else {
                    $qb->andWhere('etp_1.restricted =0');
                }
            }
        }

        if (!empty($this->filterValues['date']) && (!empty($this->filterValues['date_from']) || !empty($this->filterValues['date_to']))) {
            switch ($this->filterValues['date']) {
                case 'create':
                    if (!empty($this->filterValues['date_from'])) {
                        $qb->andWhere($qb->expr()->gte('e.create_time', $this->filterValues['date_from']));
                    }
                    if (!empty($this->filterValues['date_to'])) {
                        $qb->andWhere($qb->expr()->lte('e.create_time', $this->filterValues['date_to']));
                    }
                    break;

                case 'publish':
                    $qb->leftJoin('et', 'tree_online', 'eto_2', 'eto_2.tree_id = et.id AND eto_2.language = ' . $qb->expr()->literal($this->language));
                    if (!empty($this->filterValues['date_from'])) {
                        $qb->andWhere($qb->expr()->gte('eto_2.publish_time', $this->filterValues['date_from']));
                    }
                    if (!empty($this->filterValues['date_to'])) {
                        $qb->andWhere($qb->expr()->lte('eto_2.publish_time', $this->filterValues['date_to']));
                    }
                    break;

                case 'custom':
                    $qb->leftJoin('e', 'element_version_titles', 'evt_1', 'evt_1.eid = e.eid AND evt_1.version = e.latest_version AND evt_1.language = ' . $qb->expr()->literal($this->language));
                    if (!empty($this->filterValues['date_from'])) {
                        $qb->andWhere($qb->expr()->gte('evt_1.date', $this->filterValues['date_from']));
                    }
                    if (!empty($this->filterValues['date_to'])) {
                        $qb->andWhere($qb->expr()->lte('evt_1.date', $this->filterValues['date_to']));
                    }
                    break;
            }
        }

        if (!empty($this->filterValues['search'])) {
            $qb->join('e', 'element_data_language', 'edl_1', 'edl_1.eid = e.eid AND edl_1.version = e.latest_version AND edl_1.language = ' . $qb->expr()->literal($this->language) . ' AND edl_1.content LIKE ' . $qb->expr()->literal('%' . $this->filterValues['search'] . '%'));
            $qb->groupBy(array('et.id', 'e.latest_version'));
        }

        $event = new TreeFilterEvent($this->filterValues, $qb);
        $this->dispatcher->dispatch(TreeEvents::TREE_FILTER, $event);

        return $qb;
    }
}
