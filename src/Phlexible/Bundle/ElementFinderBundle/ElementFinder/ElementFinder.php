<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\ResultFilterInterface;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\SelectFilterInterface;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Matcher\TreeNodeMatcher;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element finder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinder
{
    const SORT_TITLE_BACKEND = '__backend_title';
    const SORT_TITLE_PAGE = '__page_title';
    const SORT_TITLE_NAVIGATION = '__navigation_title';
    const SORT_PUBLISH_DATE = '__publish_date';
    const SORT_CUSTOM_DATE = '__custom_date';

    const FIELD_SORT = 'sort_field';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var TreeNodeMatcher
     */
    private $treeNodeMatcher;

    /**
     * @var bool
     */
    private $useElementLanguageAsFallback;

    private $isNatSort = false;
    private $tidSkipList = array();

    /**
     * @param Connection               $connection
     * @param EventDispatcherInterface $dispatcher
     * @param TreeNodeMatcher          $treeNodeMatcher
     * @param bool                     $useElementLanguageAsFallback
     */
    public function __construct(
        Connection $connection,
        EventDispatcherInterface $dispatcher,
        TreeNodeMatcher $treeNodeMatcher,
        $useElementLanguageAsFallback)
    {
        $this->connection = $connection;
        $this->dispatcher = $dispatcher;
        $this->treeNodeMatcher = $treeNodeMatcher;
        $this->useElementLanguageAsFallback = (bool) $useElementLanguageAsFallback;
    }

    /**
     * Catch elements
     *
     * array(
     *    id => array(
     *        'id'      => id,
     *        'eid'     => eid,
     *        'version' => version,
     *    )
     * )
     *
     * @param ElementFinderConfig $elementCatch
     * @param array               $languages
     * @param bool                $isPreview
     * @param mixed               $filter
     * @param string              $country
     *
     * @return ElementFinderResultPool
     */
    public function find(
        ElementFinderConfig $elementCatch,
        array $languages,
        $isPreview,
        $filter = null,
        $country = null)
    {
        $resultPool = new ElementFinderResultPool(
            $elementCatch->getResultsPerPage(),
            $filter
        );

        $select = $this->createReducedSelect($elementCatch, $resultPool, $isPreview, $languages, $filter, $country);

        #$beforeEvent = new BeforeCatchGetResultPool($this, $select, $resultPool);
        #if (!$this->dispatcher->dispatch($beforeEvent)) {
        #    return $resultPool;
        #}

        #ld($elementCatch);
        #echo $select->getSQL();die;
        $items = $this->connection->fetchAll($select->getSQL());
        $newItems = array();
        foreach ($items as $item) {
            $newItems[$item['tree_id']] = $item;
        }
        $items = $newItems;

        $resultPool->setItems($items);

        if ($filter && $filter instanceof ResultFilterInterface) {
            $filter->filterResult($resultPool);
        }

        if (!$this->hasSelectSort($select, $elementCatch)) {
            // sort by tree order
            $items = $resultPool->all();
            $orderedResult = array();

            $matchedTreeIds = $this->treeNodeMatcher->flatten($resultPool->getMatchedTreeIds());
            foreach ($matchedTreeIds as $matchedTreeId) {
                if (array_key_exists($matchedTreeId, $items)) {
                    $orderedResult[$matchedTreeId] = $items[$matchedTreeId];
                }
            }

            $resultPool->setItems($items);
        } elseif ($this->isNatSort) {
            // use natsort
            $items = $resultPool->all();
            $sortedColumn = array_column($items, 'sort_field');
            natsort($sortedColumn);

            $orderedResult = array();
            foreach (array_keys($sortedColumn) as $key) {
                $orderedResult[] = $items[$key];
            }

            $resultPool->setItems($items);
        }

        if ($this->isNatSort || !$this->hasSelectSort($select, $elementCatch)) {
            // If natsort is selected the sql limit clause cannot be used
            // -> limit result by hand
            //if ($elementCatch->hasRotation()) {
            //    $limit = $elementCatch->getPoolSize() ?: 0;
            //} else {
            $limit = $elementCatch->getMaxResults() ? : 0;
            //}

            if ($limit) {
                $items = $resultPool->all();
                $items = array_slice($items, 0, $limit, true);
                $resultPool->setItems($items);
            }
        }

        #$event = new CatchGetResultPool($this, $resultPool);
        #$this->dispatcher->dispatch($event);

        return $resultPool;
    }

    /**
     * Apply filter and limit clause.
     *
     * @param ElementFinderConfig     $elementCatch
     * @param ElementFinderResultPool $resultPool
     * @param bool                    $isPreview
     * @param array                   $languages
     * @param mixed                   $filter
     * @param string                  $country
     *
     * @return QueryBuilder
     */
    private function createReducedSelect(
        ElementFinderConfig $elementCatch,
        ElementFinderResultPool $resultPool,
        $isPreview,
        array $languages,
        $filter,
        $country)
    {
        $select = $this->createFullSelect($elementCatch, $resultPool, $isPreview, $languages, $country);

        // apply filter
        if ($filter && $filter instanceof SelectFilterInterface) {
            $filter->filterSelect($elementCatch, $select);
        }

        // set sort information
        $this->applySort($select, $elementCatch, $isPreview);

        // set absolut limit clause
        if (!$this->isNatSort && $this->hasSelectSort($select, $elementCatch)) {
            $limit = $elementCatch->getMaxResults() ? : 0;

            if ($limit) {
                $select->setMaxResults($limit);
            }
        }

        return $select;
    }

    /**
     * Create select statement without reducing result sets by filters or other limitations.
     *
     * @param ElementFinderConfig     $elementCatch
     * @param ElementFinderResultPool $resultPool
     * @param bool                    $isPreview
     * @param array                   $languages
     * @param string                  $country
     *
     * @return QueryBuilder
     */
    private function createFullSelect(
        ElementFinderConfig $elementCatch,
        ElementFinderResultPool $resultPool,
        $isPreview,
        array $languages,
        $country = null)
    {
        $select = $this->connection->createQueryBuilder();
        $select
            ->select(
                array(
                    'ch.tree_id',
                    'ch.eid',
                    'ch.version',
                    'ch.is_preview',
                    'ch.elementtype_id',
                    'ch.in_navigation',
                    'ch.is_restricted',
                    'ch.published_at',
                    'ch.custom_date',
                    'ch.language',
                    //'ch.online_version',
                )
            )
            ->from('catch_lookup_element', 'ch');

        $whereChunk = array();
        $matchedTreeIds = $this->treeNodeMatcher->getMatchingTreeIdsByLanguage(
            $elementCatch->getTreeId(),
            $elementCatch->getMaxDepth(),
            $isPreview,
            $languages
        );
        $resultPool->setMatchedTreeIds($matchedTreeIds);
        foreach ($matchedTreeIds as $language => $tids) {
            $whereChunk[] = '(ch.tree_id IN (' . implode(',', $tids) . ') AND ch.language = ' . $select->expr()->literal($language) . ')';
        }
        $select->where(implode(' OR ', $whereChunk));

        if ($isPreview) {
            $select->andWhere('ch.is_preview = 1');
        } else {
            $select->andWhere('ch.is_preview = 0');
        }

        if ($elementCatch->getMetaSearch()) {
            $metaI = 0;
            foreach ($elementCatch->getMetaSearch() as $key => $value) {
                $alias = 'evmi' . ++$metaI;
                $select
                    ->join('ch', 'catch_lookup_meta', $alias, $alias . '.eid = ch.eid AND ' . $alias . '.version = ch.version AND ' . $alias . '.language = ch.language')
                    ->andWhere($select->expr()->eq("$alias.key", $select->expr()->literal($key)));

                $multiValueSelects = array();
                foreach (explode(',', $value) as $singleValue) {
                    $singleValue = trim($singleValue);
                    $multiValueSelects[] = $select->expr()->eq("$alias.value", $select->expr()->literal(mb_strtolower(html_entity_decode($singleValue, ENT_COMPAT, 'UTF-8'))));
                }

                if (count($multiValueSelects)) {
                    $select->andWhere(implode(' OR ', $multiValueSelects));
                }
            }
        }

        if (!empty($elementCatch->getElementtypeIds())) {
            $select->andWhere($select->expr()->in('ch.elementtype_id', $elementCatch->getElementtypeIds()));
        }

        if ($elementCatch->inNavigation()) {
            $select->andWhere('ch.in_navigation = 1');
        }

        if (count($this->tidSkipList)) {
            $tidSkipList = $this->tidSkipList;

            $select->andWhere($select->expr()->notIn('ch.tree_id', $tidSkipList));
        }

        if ($country) {
            if ($country !== 'global') {
                $select->andWhere(
                    '(ch.tree_id IN (SELECT DISTINCT tid FROM element_tree_context WHERE context = ? OR context = "global") OR ch.tree_id NOT IN (SELECT DISTINCT tid from element_tree_context))',
                    $country
                );
            } else {
                $select->andWhere(
                    '(ch.tree_id IN (SELECT DISTINCT tid FROM element_tree_context WHERE context = "global") OR ch.tree_id NOT IN (SELECT DISTINCT tid from element_tree_context))'
                );
            }
        }

        $select->groupBy('ch.eid');

        return $select;
    }

    /**
     * Add a sort criteria to the select statement.
     *
     * @param QueryBuilder        $select
     * @param ElementFinderConfig $elementCatch
     * @param bool                $isPreview
     */
    private function applySort(QueryBuilder $select, ElementFinderConfig $elementCatch, $isPreview)
    {
        $sortField = $elementCatch->getSortField();
        if ($sortField) {
            if (self::SORT_TITLE_BACKEND === $sortField) {
                $this->applySortByTitle($select, 'backend', $elementCatch);
            } elseif (self::SORT_TITLE_PAGE === $sortField) {
                $this->applySortByTitle($select, 'page', $elementCatch);
            } elseif (self::SORT_TITLE_NAVIGATION === $sortField) {
                $this->applySortByTitle($select, 'navigation', $elementCatch);
            } elseif (self::SORT_PUBLISH_DATE === $sortField) {
                $this->applySortByPublishDate($select, $elementCatch, $isPreview);
            } elseif (self::SORT_CUSTOM_DATE === $sortField) {
                $this->applySortByCustomDate($select, $elementCatch);
            } else {
                $this->applySortByField($select, $elementCatch);
            }
        }
    }

    /**
     * Add field sorting to select statement.
     *
     * @param QueryBuilder        $select
     * @param ElementFinderConfig $elementCatch
     */
    private function applySortByField(QueryBuilder $select, ElementFinderConfig $elementCatch)
    {
        return;
        $select
            ->addSelect(self::FIELD_SORT . '.content')
            ->join(
                'ch',
                'element_structure',
                'sort_d',
                'ch.eid = sort_d.eid AND ch.version = sort_d.version'
            )
            ->join(
                'sort_d',
                'element_structure_value',
                'sort_dl',
                'sort_d.data_id = sort_dl.data_id AND sort_d.version = sort_dl.version AND sort_d.eid = sort_dl.eid AND sort_d.ds_id = ' . $select->expr()->literal($elementCatch->getSortField()) . ' AND sort_dl.language = ch.language'
            );

        if (!$this->isNatSort) {
            $select->orderBy(self::FIELD_SORT, $elementCatch->getSortOrder());
        }
    }

    /**
     * Add title sorting to select statement.
     *
     * @param QueryBuilder        $select
     * @param string              $title
     * @param ElementFinderConfig $elementCatch
     */
    private function applySortByTitle(QueryBuilder $select, $title, ElementFinderConfig $elementCatch)
    {
        $select
            ->addSelect("sort_field.$title")
            ->leftJoin(
                'ch',
                'element_version_mapped_fields',
                'sort_t',
                'ch.eid = sort_t.eid AND ch.version = sort_t.version AND ch.language = sort_t.language'
            );

        if (!$this->isNatSort) {
            $select->orderBy(self::FIELD_SORT, $elementCatch->getSortOrder());
        }
    }

    /**
     * Add title sorting to select statement.
     *
     * @param QueryBuilder        $select
     * @param ElementFinderConfig $elementCatch
     * @param bool                $isPreview
     */
    private function applySortByPublishDate(QueryBuilder $select, ElementFinderConfig $elementCatch, $isPreview)
    {
        if (!$isPreview) {
            $select->orderBy('ch.publish_time', $elementCatch->getSortOrder());
        }
    }

    /**
     * Add title sorting to select statement.
     *
     * @param QueryBuilder        $select
     * @param ElementFinderConfig $elementCatch
     */
    private function applySortByCustomDate(QueryBuilder $select, ElementFinderConfig $elementCatch)
    {
        $select->orderBy('ch.custom_date', $elementCatch->getSortOrder());
    }

    /**
     * Check if catch definition or query have an sort field specified.
     *
     * @param QueryBuilder        $select
     * @param ElementFinderConfig $elementCatch
     *
     * @return bool
     */
    private function hasSelectSort(QueryBuilder $select, ElementFinderConfig $elementCatch)
    {
        if ($elementCatch->getSortField()) {
            return true;
        }

        $order = $select->getQueryPart('orderBy');

        if (!is_array($order) || !count($order)) {
            return false;
        }

        return true;
    }
}
