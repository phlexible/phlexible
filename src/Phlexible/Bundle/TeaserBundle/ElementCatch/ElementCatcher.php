<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\ElementCatch;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\TeaserBundle\ElementCatch\Filter\ResultFilterInterface;
use Phlexible\Bundle\TeaserBundle\ElementCatch\Filter\SelectFilterInterface;
use Phlexible\Bundle\TeaserBundle\ElementCatch\Matcher\TreeNodeMatcher;
use Phlexible\Component\Database\ConnectionManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Catch teaser
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementCatcher
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
     * @param ElementCatch $elementCatch
     * @param array        $languages
     * @param bool         $isPreview
     * @param mixed        $filter
     * @param string       $country
     *
     * @return ElementCatchResultPool
     */
    public function catchElements(
        ElementCatch $elementCatch,
        array $languages,
        $isPreview,
        $filter = null,
        $country = null)
    {
        $resultPool = new ElementCatchResultPool(
            $elementCatch->getResultsPerPage(),
            $filter
        );

        $select = $this->createReducedSelect($elementCatch, $resultPool, $isPreview, $languages, $filter, $country);

        #$beforeEvent = new BeforeCatchGetResultPool($this, $select, $resultPool);
        #if (!$this->dispatcher->dispatch($beforeEvent)) {
        #    return $resultPool;
        #}

        #ld($elementCatch);
        #echo $select.PHP_EOL;
        $items = $select->getAdapter()->fetchAssoc($select);

        $resultPool->setItems($items);

        if ($filter && $filter instanceof ResultFilterInterface) {
            $filter->filterResult($resultPool);
        }

        if (!$this->hasSelectSort($select, $elementCatch)) {
            // sort by tree order
            $items = $resultPool->getItems();
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
            $items = $resultPool->getItems();
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
                $items = $resultPool->getItems();
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
     * @param ElementCatch           $elementCatch
     * @param ElementCatchResultPool $resultPool
     * @param bool                   $isPreview
     * @param array                  $languages
     * @param mixed                  $filter
     * @param string                 $country
     *
     * @return \Zend_Db_Select
     */
    private function createReducedSelect(
        ElementCatch $elementCatch,
        ElementCatchResultPool $resultPool,
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
                $select->limit($limit);
            }
        }

        return $select;
    }

    /**
     * Create select statement without reducing result sets by filters or other limitations.
     *
     * @param ElementCatch           $elementCatch
     * @param ElementCatchResultPool $resultPool
     * @param bool                   $isPreview
     * @param array                  $languages
     * @param string                 $country
     *
     * @return \Zend_Db_Select
     */
    private function createFullSelect(
        ElementCatch $elementCatch,
        ElementCatchResultPool $resultPool,
        $isPreview,
        array $languages,
        $country = null)
    {
        $select = $this->connection
            ->select()
            ->from(
                array('ch' => $this->connection->prefix . 'catch_lookup_element'),
                array(
                    'tree_id AS tid',
                    'eid',
                    'version',
                    'is_preview AS preview',
                    'elementtype_id',
                    'in_navigation',
                    'is_restricted AS restricted',
                    'published_at AS publish_time',
                    'custom_date',
                    'language',
                    //'online_version',
                )
            );

        $whereChunk = array();
        $matchedTreeIds = $this->treeNodeMatcher->getMatchingTreeIdsByLanguage(
            $elementCatch->getTreeId(),
            $elementCatch->getMaxDepth(),
            $isPreview,
            $languages
        );
        $resultPool->setMatchedTreeIds($matchedTreeIds);
        foreach ($matchedTreeIds as $language => $tids) {
            $whereChunk[] = '(ch.tree_id IN (' . $this->connection->quote($tids) . ') AND ch.language = ' . $this->connection->quote(
                    $language
                ) . ')';
        }
        $select->where(implode(' OR ', $whereChunk));

        if ($isPreview) {
            $select->where('ch.is_preview = ?', 1);
        } else {
            $select->where('ch.is_preview = ?', 0);
        }

        if ($elementCatch->getMetaSearch()) {
            $metaI = 0;
            foreach ($elementCatch->getMetaSearch() as $key => $value) {
                $alias = 'evmi' . ++$metaI;
                $select
                    ->join(
                        array($alias => $this->connection->prefix . 'catch_lookup_meta'),
                        $alias . '.eid = ch.eid AND ' . $alias . '.version = ch.version AND ' . $alias . '.language = ch.language',
                        array()
                    )
                    ->where($alias . '.key = ?', $key);

                $multiValueSelects = array();
                foreach (explode(',', $value) as $singleValue) {
                    $singleValue = trim($singleValue);
                    $multiValueSelects[] = $this->connection->quoteInto(
                        "$alias.value = ?",
                        mb_strtolower(html_entity_decode($singleValue, ENT_COMPAT, 'UTF-8'))
                    );
                }

                if (count($multiValueSelects)) {
                    $select->where(implode(' OR ', $multiValueSelects));
                }
            }
        }

        $select->where('ch.elementtype_id IN (?)', $elementCatch->getElementtypeIds());

        if ($elementCatch->inNavigation()) {
            $select->where('ch.in_navigation = ?', 1);
        }

        if (count($this->tidSkipList)) {
            $tidSkipList = $this->tidSkipList;

            $select->where('ch.tree_id NOT IN (?)', $tidSkipList);
        }

        if ($country) {
            if ($country !== 'global') {
                $select->where(
                    '(ch.tree_id IN (SELECT DISTINCT tid FROM ' . $this->connection->prefix . 'element_tree_context WHERE context = ? OR context = "global") OR ch.tree_id NOT IN (SELECT DISTINCT tid from ' . $this->connection->prefix . 'element_tree_context))',
                    $country
                );
            } else {
                $select->where(
                    '(ch.tree_id IN (SELECT DISTINCT tid FROM ' . $this->connection->prefix . 'element_tree_context WHERE context = "global") OR ch.tree_id NOT IN (SELECT DISTINCT tid from ' . $this->connection->prefix . 'element_tree_context))'
                );
            }
        }

        $select->group('ch.eid');

        return $select;
    }

    /**
     * Add a sort criteria to the select statement.
     *
     * @param \Zend_Db_Select $select
     * @param ElementCatch    $elementCatch
     * @param bool            $isPreview
     */
    private function applySort(\Zend_Db_Select $select, ElementCatch $elementCatch, $isPreview)
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
                $this->applySortByField($select);
            }
        }
    }

    /**
     * Add field sorting to select statement.
     *
     * @param \Zend_Db_Select $select
     * @param ElementCatch    $elementCatch
     */
    private function applySortByField(\Zend_Db_Select $select)
    {
        $db = $select->getAdapter();

        $select
            ->join(
                array('sort_d' => $db->prefix . 'element_data'),
                'ch.eid = sort_d.eid AND ch.version = sort_d.version',
                array()
            )
            ->join(
                array('sort_dl' => $db->prefix . 'element_data_language'),
                'sort_d.data_id = sort_dl.data_id AND sort_d.version = sort_dl.version AND sort_d.eid = sort_dl.eid AND sort_d.ds_id = ' . $db->quote(
                    $elementCatch->getSortField()
                ) . ' AND sort_dl.language = ch.language',
                array(self::FIELD_SORT => 'content')
            );

        if (!$this->isNatSort) {
            $select->order(self::FIELD_SORT . ' ' . $elementCatch->getSortOrder());
        }
    }

    /**
     * Add title sorting to select statement.
     *
     * @param \Zend_Db_Select $select
     * @param string          $title
     * @param ElementCatch    $elementCatch
     */
    private function applySortByTitle(\Zend_Db_Select $select, $title, ElementCatch $elementCatch)
    {
        $select
            ->joinLeft(
                array('sort_t' => $select->getAdapter()->prefix . 'element_version_titles'),
                'ch.eid = sort_t.eid AND ch.version = sort_t.version',
                array('sort_field' => $title)
            )
            ->where('sort_t.language = ch.language');

        if (!$this->isNatSort) {
            $select->order(self::FIELD_SORT . ' ' . $elementCatch->getSortOrder());
        }
    }

    /**
     * Add title sorting to select statement.
     *
     * @param \Zend_Db_Select $select
     * @param ElementCatch    $elementCatch
     * @param bool            $isPreview
     */
    private function applySortByPublishDate(\Zend_Db_Select $select, ElementCatch $elementCatch, $isPreview)
    {
        if (!$isPreview) {
            $select->order('ch.publish_time ' . $elementCatch->getSortOrder());
        }
    }

    /**
     * Add title sorting to select statement.
     *
     * @param \Zend_Db_Select $select
     * @param ElementCatch    $elementCatch
     */
    private function applySortByCustomDate(\Zend_Db_Select $select, ElementCatch $elementCatch)
    {
        $select->order('ch.custom_date ' . $elementCatch->getSortOrder());
    }

    /**
     * Check if catch definition or query have an sort field specified.
     *
     * @param \Zend_Db_Select $select
     * @param ElementCatch    $elementCatch
     *
     * @return bool
     */
    private function hasSelectSort(\Zend_Db_Select $select, ElementCatch $elementCatch)
    {
        if ($elementCatch->getSortField()) {
            return true;
        }

        $order = $select->getPart(\Zend_Db_Select::ORDER);

        if (!is_array($order) || !count($order)) {
            return false;
        }

        return true;
    }
}
