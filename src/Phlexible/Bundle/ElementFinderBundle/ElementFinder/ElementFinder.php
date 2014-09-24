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
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter\QueryEnhancerInterface;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\Matcher\TreeNodeMatcherInterface;
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
     * @var TreeNodeMatcherInterface
     */
    private $treeNodeMatcher;

    /**
     * @var bool
     */
    private $useElementLanguageAsFallback;

    /**
     * @var bool
     */
    private $isNatSort = false;

    /**
     * @var array
     */
    private $tidSkipList = array();

    /**
     * @param Connection               $connection
     * @param EventDispatcherInterface $dispatcher
     * @param TreeNodeMatcherInterface $treeNodeMatcher
     * @param bool                     $useElementLanguageAsFallback
     */
    public function __construct(
        Connection $connection,
        EventDispatcherInterface $dispatcher,
        TreeNodeMatcherInterface $treeNodeMatcher,
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
     *
     * @return ResultPool
     */
    public function find(
        ElementFinderConfig $elementCatch,
        array $languages,
        $isPreview,
        $filter = null)
    {
        $matchedTreeIds = $this->treeNodeMatcher->getMatchingTreeIdsByLanguage(
            $elementCatch->getTreeId(),
            $elementCatch->getMaxDepth(),
            $isPreview,
            $languages
        );

        $qb = $this->createSelect($elementCatch, $isPreview, $languages, $matchedTreeIds, $filter);

        #$beforeEvent = new BeforeCatchGetResultPool($this, $qb, $resultPool);
        #if ($this->dispatcher->dispatch($beforeEvent)->isPropagationStopped()) {
        #    return $resultPool;
        #}

        $items = $this->connection->fetchAll($qb->getSQL());
        $newItems = array();
        foreach ($items as $item) {
            $newItems[$item['tree_id']] = $item;
        }
        $items = $newItems;

        /*
        if ($filter && $filter instanceof ResultFilterInterface) {
            $filter->filterResult($resultPool);
        }
        */

        if (!$elementCatch->getSortField() && $matchedTreeIds) {
            // sort by tree order
            $orderedResult = array();

            $treeIds = $this->treeNodeMatcher->flatten($matchedTreeIds);
            foreach ($treeIds as $treeId) {
                if (array_key_exists($treeId, $items)) {
                    $orderedResult[$treeId] = $items[$treeId];
                }
            }
            sort($orderedResult);
            $items = $orderedResult;
        } elseif ($this->isNatSort) {
            // sort by sort field
            $sortedColumn = array_column($items, 'sort_field');
            if ($this->isNatSort) {
                // use natsort
                natsort($sortedColumn);
            } else {
                // use sort
                sort($sortedColumn);
            }

            $orderedResult = array();
            foreach (array_keys($sortedColumn) as $key) {
                $orderedResult[] = $items[$key];
            }
            $items = $orderedResult;
        }

        $resultPool = new ResultPool();
        $resultPool->setQuery((string) $qb);

        foreach ($items as $row) {
            $resultPool->addItem(
                new ResultItem(
                    $row['tree_id'],
                    $row['eid'],
                    $row['version'],
                    $row['language'],
                    $row['elementtype_id'],
                    $row['in_navigation'],
                    $row['is_restricted'],
                    $row['published_at'],
                    $row['custom_date']
                )
            );
        }

        #$event = new CatchGetResultPool($this, $resultPool);
        #$this->dispatcher->dispatch($event);

        return $resultPool;
    }

    /**
     * Apply filter and limit clause.
     *
     * @param ElementFinderConfig $elementCatch
     * @param bool                $isPreview
     * @param array               $languages
     * @param array|null          $matchedTreeIds
     * @param mixed               $filter
     *
     * @return QueryBuilder
     */
    private function createSelect(
        ElementFinderConfig $elementCatch,
        $isPreview,
        array $languages,
        array $matchedTreeIds = null,
        $filter)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
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

        if ($matchedTreeIds === null) {
            $qb->where('1 = 0');

            return $qb;
        }

        $or = $qb->expr()->orX();
        foreach ($matchedTreeIds as $language => $tids) {
            $or->add(
                $qb->expr()->andX(
                    $qb->expr()->in('ch.tree_id', $tids),
                    $qb->expr()->eq('ch.language', $qb->expr()->literal($language))
                )
            );
        }
        $qb->where($or);

        if ($isPreview) {
            $qb->andWhere('ch.is_preview = 1');
        } else {
            $qb->andWhere('ch.is_preview = 0');
        }

        if ($elementCatch->getMetaSearch()) {
            $metaI = 0;
            foreach ($elementCatch->getMetaSearch() as $key => $value) {
                $alias = 'evmi' . ++$metaI;
                $qb
                    ->join('ch', 'catch_lookup_meta', $alias, $alias . '.eid = ch.eid AND ' . $alias . '.version = ch.version AND ' . $alias . '.language = ch.language')
                    ->andWhere($qb->expr()->eq("$alias.key", $qb->expr()->literal($key)));

                $multiValueSelects = array();
                foreach (explode(',', $value) as $singleValue) {
                    $singleValue = trim($singleValue);
                    $multiValueSelects[] = $qb->expr()->eq("$alias.value", $qb->expr()->literal(mb_strtolower(html_entity_decode($singleValue, ENT_COMPAT, 'UTF-8'))));
                }

                if (count($multiValueSelects)) {
                    $qb->andWhere(implode(' OR ', $multiValueSelects));
                }
            }
        }

        if (!empty($elementCatch->getElementtypeIds())) {
            $qb->andWhere($qb->expr()->in('ch.elementtype_id', $elementCatch->getElementtypeIds()));
        }

        if ($elementCatch->inNavigation()) {
            $qb->andWhere('ch.in_navigation = 1');
        }

        if (count($this->tidSkipList)) {
            $tidSkipList = $this->tidSkipList;

            $qb->andWhere($qb->expr()->notIn('ch.tree_id', $tidSkipList));
        }

        /*
        if ($country) {
            if ($country !== 'global') {
                $qb->andWhere(
                    '(ch.tree_id IN (SELECT DISTINCT tid FROM element_tree_context WHERE context = ? OR context = "global") OR ch.tree_id NOT IN (SELECT DISTINCT tid from element_tree_context))',
                    $country
                );
            } else {
                $qb->andWhere(
                    '(ch.tree_id IN (SELECT DISTINCT tid FROM element_tree_context WHERE context = "global") OR ch.tree_id NOT IN (SELECT DISTINCT tid from element_tree_context))'
                );
            }
        }
        */

        $qb->groupBy('ch.eid');

        // apply filter
        if ($filter && $filter instanceof QueryEnhancerInterface) {
            $filter->enhance($elementCatch, $qb);
        }

        // set sort information
        $this->applySort($qb, $elementCatch, $isPreview);

        return $qb;
    }

    /**
     * Add a sort criteria to the select statement.
     *
     * @param QueryBuilder        $qb
     * @param ElementFinderConfig $elementCatch
     * @param bool                $isPreview
     */
    private function applySort(QueryBuilder $qb, ElementFinderConfig $elementCatch, $isPreview)
    {
        $sortField = $elementCatch->getSortField();
        if (!$sortField) {
            return;
        }

        if (self::SORT_TITLE_BACKEND === $sortField) {
            $this->applySortByTitle($qb, 'backend', $elementCatch);
        } elseif (self::SORT_TITLE_PAGE === $sortField) {
            $this->applySortByTitle($qb, 'page', $elementCatch);
        } elseif (self::SORT_TITLE_NAVIGATION === $sortField) {
            $this->applySortByTitle($qb, 'navigation', $elementCatch);
        } elseif (self::SORT_PUBLISH_DATE === $sortField) {
            $this->applySortByPublishDate($qb, $elementCatch, $isPreview);
        } elseif (self::SORT_CUSTOM_DATE === $sortField) {
            $this->applySortByCustomDate($qb, $elementCatch);
        } else {
            $this->applySortByField($qb, $elementCatch);
        }
    }

    /**
     * Add field sorting to select statement.
     *
     * @param QueryBuilder        $qb
     * @param ElementFinderConfig $elementCatch
     */
    private function applySortByField(QueryBuilder $qb, ElementFinderConfig $elementCatch)
    {
        $qb
            ->addSelect('sort_esv.content AS sort_field')
            ->join(
                'ch',
                'element_structure',
                'sort_es',
                'ch.eid = sort_es.eid AND ch.version = sort_es.version'
            )
            ->join(
                'sort_d',
                'element_structure_value',
                'sort_esv',
                'sort_es.data_id = sort_esvl.data_id AND sort_es.version = sort_esv.version AND sort_es.eid = sort_esv.eid AND sort_es.ds_id = ' . $qb->expr()->literal($elementCatch->getSortField()) . ' AND sort_esv.language = ch.language'
            );
    }

    /**
     * Add title sorting to select statement.
     *
     * @param QueryBuilder        $qb
     * @param string              $title
     * @param ElementFinderConfig $elementCatch
     */
    private function applySortByTitle(QueryBuilder $qb, $title, ElementFinderConfig $elementCatch)
    {
        $qb
            ->addSelect("sort.$title AS sort_field")
            ->leftJoin(
                'ch',
                'element_version_mapped_fields',
                'sort',
                'ch.eid = sort_t.eid AND ch.version = sort_t.version AND ch.language = sort_t.language'
            );

        if (!$this->isNatSort) {
            $qb->orderBy(self::FIELD_SORT, $elementCatch->getSortDir());
        }
    }

    /**
     * Add title sorting to select statement.
     *
     * @param QueryBuilder        $qb
     * @param ElementFinderConfig $elementCatch
     * @param bool                $isPreview
     */
    private function applySortByPublishDate(QueryBuilder $qb, ElementFinderConfig $elementCatch, $isPreview)
    {
        if (!$isPreview) {
            $qb
                ->addSelect("ch.publish_time AS sort_field");
        }
    }

    /**
     * Add title sorting to select statement.
     *
     * @param QueryBuilder        $qb
     * @param ElementFinderConfig $elementCatch
     */
    private function applySortByCustomDate(QueryBuilder $qb, ElementFinderConfig $elementCatch)
    {
        $qb
            ->addSelect("ch.custom_date AS sort_field");
    }
}
