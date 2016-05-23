<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Phlexible\Bundle\TreeBundle\Event\TreeFilterEvent;
use Phlexible\Bundle\TreeBundle\Model\TreeFilterInterface;
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
class TreeFilter implements TreeFilterInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $filterValues = [];

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

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->storage->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function setFilterValues(array $filterValues)
    {
        $this->filterValues = $filterValues;
        $this->storage->set('filterValues', $filterValues);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterValues()
    {
        return $this->filterValues;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortMode($sortMode)
    {
        $this->sortMode = $sortMode;
        $this->storage->set('sortMode', $sortMode);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortMode()
    {
        return $this->sortMode;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortDir($sortDir)
    {
        $this->sortDir = $sortDir;
        $this->storage->set('sortDir', $sortDir);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortDir()
    {
        return $this->sortDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getPager($id)
    {
        $ids = $this->fetchAllIds();
        $pos = array_search($id, $ids);

        $pager = [
            'first'   => $ids[0],
            'prev'    => null,
            'current' => $id,
            'next'    => null,
            'last'    => $ids[count($ids) - 1],
            'pos'     => $pos + 1,
            'total'   => count($ids),
        ];

        if ($pos > 0) {
            $pager['prev'] = $ids[$pos - 1];
        }

        if ($pos < count($ids) - 1) {
            $pager['next'] = $ids[$pos + 1];
        }

        return $pager;
    }

    /**
     * {@inheritdoc}
     */
    public function getCount()
    {
        return $this->fetchCount();
    }

    /**
     * {@inheritdoc}
     */
    public function getIds($limit = null, $start = null)
    {
        return $this->fetchRangeIds($limit, $start);
    }

    /**
     * @return int
     */
    private function fetchCount()
    {
        $qb = $this->createFilterQueryBuilder();
        $qb->select(['t.id']);

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
        $qb->select(['t.id']);

        $rows = $this->connection->fetchAll($qb->getSQL());

        $ids = [];
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
        $qb->select(['t.id', 'e.latest_version']);

        if (null !== $limit) {
            $qb->setFirstResult($start);
            $qb->setMaxResults($limit);
        }

        $rows = $this->connection->fetchAll($qb->getSQL());

        $ids = [];
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
                    ->leftJoin('e', 'element_version', 'ev', 'e.eid = ev.eid AND e.latest_version = ev.version')
                    ->leftJoin('ev', 'element_version_mapped_field', 'evmf_sort', 'evmf_sort.element_version_id = ev.id AND evmf_sort.language = ' . $qb->expr()->literal($this->language))
                    ->orderBy('evmf_sort.backend', $this->sortDir);
                break;

            case 'create_time':
                $qb->orderBy('t.created_at', $this->sortDir);
                break;

            case 'publish_time':
                $qb
                    ->leftJoin('t', 'tree_online', 'to_sort', 'to_sort.tree_id = t.id AND to_sort.language = ' . $qb->expr()->literal($this->language))
                    ->orderBy('to_sort.published_at', $this->sortDir);
                break;

            case 'custom_date':
                $qb
                    ->leftJoin('e', 'element_version', 'ev', 'e.eid = ev.eid AND e.latest_version = ev.version')
                    ->leftJoin('ev', 'element_version_mapped_field', 'evmf_sort', 'evmf_sort.element_version_id = ev.id AND evmf_sort.language = ' . $qb->expr()->literal($this->language))
                    ->orderBy('evmf_sort.date', $this->sortDir);
                break;

            case 'sort':
            default:
                $qb->orderBy('t.sort', $this->sortDir);
                break;
        }

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    private function createFilterQueryBuilder()
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->from('tree', 't')
            ->leftJoin('t', 'element', 'e', 't.type = "element" AND t.type_id = e.eid')
            ->where($qb->expr()->eq('t.parent_id', $this->tid));

        if (!empty($this->filterValues['status'])) {
            $qb->leftJoin('t', 'tree_online', 'to1', 'to_1.tree_id = t.id AND to_1.language = ' . $qb->expr()->literal($this->language));

            $where = '0';

            $filterStatus = explode(',', $this->filterValues['status']);

            if (in_array('online', $filterStatus)) {
                $where .= ' OR to_1.version = e.latest_version';
            }
            if (in_array('async', $filterStatus)) {
                $qb->leftJoin('to_1', 'tree_hash', 'th_1', 'th_1.tid = to_1.tree_id AND th_1.language = to_1.language AND th_1.version = to_1.version');
                $qb->leftJoin('to_1', 'tree_hash', 'th_2', 'th_2.tid = to_1.tree_id AND th_2.language = to_1.language AND th_2.version = e.latest_version');

                $where .= ' OR (to_1.version != e.latest_version AND th_1.hash != th_2.hash)';
            }
            if (in_array('offline', $filterStatus)) {
                $where .= ' OR to_1.version IS NULL';
            }

            $qb->andWhere($where);
        }

        if (!empty($this->filterValues['navigation']) || !empty($this->filterValues['restricted'])) {
            $qb->leftJoin('t', 'tree_page', 'tp_1', 'tp_1.tree_id = t.id AND tp_1.version = e.latest_version');

            if (!empty($this->filterValues['navigation'])) {
                if ($this->filterValues['navigation'] == 'in_navigation') {
                    $qb->andWhere('tp_1.navigation = 1');
                } else {
                    $qb->andWhere('tp_1.navigation = 0');
                }
            }

            if (!empty($this->filterValues['restricted'])) {
                if ($this->filterValues['restricted'] == 'is_restricted') {
                    $qb->andWhere('tp_1.restricted = 1');
                } else {
                    $qb->andWhere('tp_1.restricted =0');
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
                    $qb->leftJoin('t', 'tree_online', 'to_2', 'to_2.tree_id = t.id AND to_2.language = ' . $qb->expr()->literal($this->language));
                    if (!empty($this->filterValues['date_from'])) {
                        $qb->andWhere($qb->expr()->gte('to_2.publish_time', $this->filterValues['date_from']));
                    }
                    if (!empty($this->filterValues['date_to'])) {
                        $qb->andWhere($qb->expr()->lte('to_2.publish_time', $this->filterValues['date_to']));
                    }
                    break;

                case 'custom':
                    $qb->leftJoin('e', 'element_version_titles', 'evmf_1', 'evmf_1.eid = e.eid AND evmf_1.version = e.latest_version AND evmf_1.language = ' . $qb->expr()->literal($this->language));
                    if (!empty($this->filterValues['date_from'])) {
                        $qb->andWhere($qb->expr()->gte('evmf_1.date', $this->filterValues['date_from']));
                    }
                    if (!empty($this->filterValues['date_to'])) {
                        $qb->andWhere($qb->expr()->lte('evmf_1.date', $this->filterValues['date_to']));
                    }
                    break;
            }
        }

        if (!empty($this->filterValues['search'])) {
            $qb->join('e', 'element_data_language', 'edl_1', 'edl_1.eid = e.eid AND edl_1.version = e.latest_version AND edl_1.language = ' . $qb->expr()->literal($this->language) . ' AND edl_1.content LIKE ' . $qb->expr()->literal('%' . $this->filterValues['search'] . '%'));
            $qb->groupBy(['t.id', 'e.latest_version']);
        }

        $event = new TreeFilterEvent($this->filterValues, $qb);
        $this->dispatcher->dispatch(TreeEvents::TREE_FILTER, $event);

        return $qb;
    }
}
