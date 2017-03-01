<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Phlexible\Bundle\TreeBundle\Event\TreeFilterEvent;
use Phlexible\Bundle\TreeBundle\Model\TreeFilterInterface;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Tree filter.
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
     * @var TreeFilterStorage
     */
    private $storage;

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

        $key = 'elements_filter_'.$tid.'_'.$language;
        if ($session->has($key)) {
            $this->storage = $session->get($key);
        } else {
            $this->storage = new TreeFilterStorage();
            $session->set($key, $this->storage);
        }

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
            'first' => $ids[0],
            'prev' => null,
            'current' => $id,
            'next' => null,
            'last' => $ids[count($ids) - 1],
            'pos' => $pos + 1,
            'total' => count($ids),
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
                    ->leftJoin('e', 'element_version', 'ev', $qb->expr()->andX(
                        $qb->expr()->eq('e.eid', 'ev.eid'),
                        $qb->expr()->eq('e.latest_version', 'ev.version')
                    ))
                    ->leftJoin('ev', 'element_version_mapped_field', 'evmf_sort', $qb->expr()->andX(
                        $qb->expr()->eq('evmf_sort.element_version_id', 'ev.id'),
                        $qb->expr()->eq('evmf_sort.language', $qb->expr()->literal($this->language))
                    ))
                    ->orderBy('evmf_sort.backend', $this->sortDir);
                break;

            case 'create_time':
                $qb->orderBy('t.created_at', $this->sortDir);
                break;

            case 'publish_time':
                $qb
                    ->leftJoin('t', 'tree_online', 'to_sort', $qb->expr()->andX(
                        $qb->expr()->eq('to_sort.tree_id', 't.id'),
                        $qb->expr()->eq('to_sort.language', $qb->expr()->literal($this->language))
                    ))
                    ->orderBy('to_sort.published_at', $this->sortDir);
                break;

            case 'custom_date':
                $qb
                    ->leftJoin('e', 'element_version', 'ev', $qb->expr()->andX(
                        $qb->expr()->eq('e.eid', 'ev.eid'),
                        $qb->expr()->eq('e.latest_version', 'ev.version')
                    ))
                    ->leftJoin('ev', 'element_version_mapped_field', 'evmf_sort', $qb->expr()->andX(
                        $qb->expr()->eq('evmf_sort.element_version_id', 'ev.id'),
                        $qb->expr()->eq('evmf_sort.language', $qb->expr()->literal($this->language))
                    ))
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
            ->leftJoin('t', 'element', 'e', $qb->expr()->eq('t.type_id', 'e.eid'))
            ->where($qb->expr()->eq('t.parent_id', $this->tid));

        if (!empty($this->filterValues['status'])) {
            $qb->leftJoin('t', 'tree_online', 'to_1', $qb->expr()->andX(
                $qb->expr()->eq('to_1.tree_id', 't.id'),
                $qb->expr()->eq('to_1.language', $qb->expr()->literal($this->language))
            ));

            $or = $qb->expr()->orX();

            $filterStatus = explode(',', $this->filterValues['status']);

            if (in_array('online', $filterStatus)) {
                $or->add($qb->expr()->eq('to_1.version', 'e.latest_version'));
            }
            if (in_array('async', $filterStatus)) {
                $or->add($qb->expr()->neq('to_1.version', 'e.latest_version'));
            }
            if (in_array('offline', $filterStatus)) {
                $or->add($qb->expr()->isNull('to_1.version'));
            }

            $qb->andWhere($or);
        }

        if (!empty($this->filterValues['navigation'])) {
            if ($this->filterValues['navigation'] === 'in_navigation') {
                $qb->andWhere($qb->expr()->eq('t.in_navigation', 1));
            } else {
                $qb->andWhere($qb->expr()->eq('t.in_navigation', 0));
            }
        }

        if (0 && !empty($this->filterValues['restricted'])) {
            if ($this->filterValues['restricted'] === 'is_restricted') {
                $qb->andWhere($qb->expr()->eq('tp_1.restricted', 1));
            } else {
                $qb->andWhere($qb->expr()->eq('tp_1.restricted', 0));
            }
        }

        if (!empty($this->filterValues['date'])) {
            switch ($this->filterValues['date']) {
                case 'create':
                    if (!empty($this->filterValues['date_from'])) {
                        $qb->andWhere($qb->expr()->gte('e.created_at', $qb->expr()->literal($this->filterValues['date_from'])));
                    }
                    if (!empty($this->filterValues['date_to'])) {
                        $qb->andWhere($qb->expr()->lte('e.created_at', $qb->expr()->literal($this->filterValues['date_to'])));
                    }
                    break;

                case 'publish':
                    $qb->leftJoin('t', 'tree_online', 'to_2', 'to_2.tree_id = t.id AND to_2.language = '.$qb->expr()->literal($this->language));
                    if (!empty($this->filterValues['date_from'])) {
                        $qb->andWhere($qb->expr()->gte('to_2.published_at', $qb->expr()->literal($this->filterValues['date_from'])));
                    }
                    if (!empty($this->filterValues['date_to'])) {
                        $qb->andWhere($qb->expr()->lte('to_2.published_at', $qb->expr()->literal($this->filterValues['date_to'])));
                    }
                    if (empty($this->filterValues['date_from']) && empty($this->filterValues['date_to'])) {
                        $qb->andWhere($qb->expr()->isNull('to_2.published_at'));
                    }
                    break;

                case 'custom':
                    $qb
                        ->leftJoin('e', 'element_version', 'ev_1', $qb->expr()->andX(
                            $qb->expr()->eq('ev_1.eid', 'e.eid'),
                            $qb->expr()->eq('ev_1.version', 'e.latest_version')
                        ))
                        ->leftJoin('ev_1', 'element_version_mapped_field', 'evmf_1', $qb->expr()->eq('evmf_1.element_version_id', 'ev_1.id'));
                    if (!empty($this->filterValues['date_from'])) {
                        $qb->andWhere($qb->expr()->gte('evmf_1.date', $qb->expr()->literal($this->filterValues['date_from'])));
                    }
                    if (!empty($this->filterValues['date_to'])) {
                        $qb->andWhere($qb->expr()->lte('evmf_1.date', $qb->expr()->literal($this->filterValues['date_to'])));
                    }
                    if (empty($this->filterValues['date_from']) && empty($this->filterValues['date_to'])) {
                        $qb->andWhere($qb->expr()->isNull('evmf_1.date'));
                    }
                    break;
            }
        }

        if (!empty($this->filterValues['search'])) {
            $qb->join('e', 'element_structure_value', 'esv_1', $qb->expr()->andX(
                $qb->expr()->eq('esv_1.eid', 'e.eid'),
                $qb->expr()->eq('esv_1.version', 'e.latest_version'),
                $qb->expr()->eq('esv_1.language', $qb->expr()->literal($this->language)),
                $qb->expr()->like('esv_1.content', $qb->expr()->literal('%'.$this->filterValues['search'].'%'))
            ));
            $qb->groupBy(['t.id', 'e.latest_version']);
        }

        $event = new TreeFilterEvent($this->filterValues, $qb);
        $this->dispatcher->dispatch(TreeEvents::TREE_FILTER, $event);

        return $qb;
    }
}
