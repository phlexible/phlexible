<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\ElementCatch\Filter;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\TeaserBundle\ElementCatch\ElementCatchResultPool;

/**
 * Abstract filter
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractFilter implements ResultFilterInterface, SelectFilterInterface
{
    const SKIP_WHERE = '__SKIP_WHERE__';

    /**
     * @var Makeweb_Teasers_Catch
     */
    protected $_catch;

    /**
     * Names of formular fields used by this filter.
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * ds_id cache
     *
     * @var array
     */
    private static $_dsIds = array();

    /**
     * Returns true if filter is used in this request.
     *
     * @return bool
     */
    public function isActive()
    {
        $values = $this->_catch->getFilterValues();

        foreach ($this->_fields as $field) {
            if (isset($values[$field])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get active params used in this request.
     *
     * @return array
     */
    public function getActiveParams()
    {
        $values = $this->_catch->getFilterValues();
        foreach ($values as $key => $value) {
            if (empty($value)) {
                unset($values[$key]);
            }
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function filterResult(ElementCatchResultPool $resultPool)
    {
        // do nothing
    }

    /**
     * Get a specific ds_id.
     *
     * @param Connection $db
     * @param array      $elementtypeIds
     * @param string     $workingTitle
     *
     * @return array
     */
    protected function _getFieldDsIds(Connection $db, array $elementtypeIds, $workingTitle)
    {
        if (!array_key_exists($workingTitle, self::$_dsIds)) {
            $select = $db
                ->select()
                ->distinct()
                ->from($db->prefix . 'elementtype_structure', 'ds_id')
                ->where('name = ?', $workingTitle)
                ->where('elementtype_id IN (?)', $elementtypeIds);

            $result = $db->fetchCol($select);

            self::$_dsIds[$workingTitle] = $result;

            if (!count($result)) {
                MWF_Log::warn('No field ds_id found for working title: ' . $workingTitle);
            }
        }

        return self::$_dsIds[$workingTitle];
    }

    /**
     * Fetch possible content.
     *
     * @return array
     */
    protected function _getContent()
    {
        $select = $this->_catch->createFullSelect();

        $db = $select->getAdapter();

        // join each field
        foreach (func_get_args() as $idx => $dsIds) {
            $dsIds = (array) $dsIds;

            if (!count($dsIds)) {
                continue;
            }

            $select
                ->join(
                    array("filter_d_$idx" => $db->prefix . 'element_data'),
                    "ch.eid = filter_d_$idx.eid AND ch.version = filter_d_$idx.version",
                    array()
                )
                ->join(
                    array("filter_dl_$idx" => $db->prefix . 'element_data_language'),
                    "filter_d_$idx.eid = filter_dl_$idx.eid AND " .
                    "filter_d_$idx.version = filter_dl_$idx.version AND " .
                    "filter_d_$idx.data_id = filter_dl_$idx.data_id AND " .
                    "filter_dl_$idx.language = " . $db->quote($this->_catch->getLanguage()),
                    array("filter_content_$idx" => "filter_dl_$idx.content")
                )
                ->where("filter_d_$idx.ds_id IN (?)", $dsIds)
                ->group("filter_content_$idx");
        }

        $result = $select->getAdapter()->fetchAll($select);

        return $result;
    }

    /**
     * Join a filed
     *
     * @param \Zend_Db_Select $select
     * @param int|string      $index
     * @param array           $dsIds
     * @param string|array    $whereValue
     * @param string|array    $whereExpr
     */
    protected function _joinField(
        \Zend_Db_Select $select,
        $index,
        array $dsIds,
        $whereValue = null,
        $whereExpr = null)
    {
        $this->_internalJoinField('join', $select, $index, $dsIds, $whereValue, $whereExpr);
    }

    /**
     * Join a filed
     *
     * @param \Zend_Db_Select $select
     * @param int|string      $index
     * @param array           $dsIds
     * @param string          $whereValue
     * @param string          $whereExpr
     */
    protected function _joinLeftField(
        \Zend_Db_Select $select,
        $index,
        array $dsIds,
        $whereValue = null,
        $whereExpr = null)
    {
        $this->_internalJoinField('joinLeft', $select, $index, $dsIds, $whereValue, $whereExpr);
    }

    /**
     * Join a field
     *
     * @param string          $joinMethod
     * @param \Zend_Db_Select $select
     * @param int|string      $index
     * @param array           $dsIds
     * @param string|array    $whereValue
     * @param string          $whereExpr
     */
    protected function _internalJoinField(
        $joinMethod,
        \Zend_Db_Select $select,
        $index,
        array $dsIds,
        $whereValue = null,
        $whereExpr = null)
    {
        if (!count($dsIds)) {
            return;
        }

        $db = $select->getAdapter();

        $filterD = 'filter_d_' . $index;
        $filterDl = 'filter_dl_' . $index;

        if (!$whereExpr) {
            $whereExpr = $filterDl . '.content';
        }

        $language = 'de'; //$this->_catch->getLanguage();
        $select
            ->$joinMethod(
                array($filterD => $db->prefix . 'element_data'),
                'ch.eid = ' . $filterD . '.eid AND ' .
                'ch.version = ' . $filterD . '.version AND ' .
                $db->quoteInto($filterD . '.ds_id IN (?)', $dsIds),
                array()
            )
            ->$joinMethod(
                array($filterDl => $db->prefix . 'element_data_language'),
                $filterD . '.eid = ' . $filterDl . '.eid AND ' .
                $filterD . '.version = ' . $filterDl . '.version AND ' .
                $filterD . '.data_id = ' . $filterDl . '.data_id AND ' .
                $filterDl . '.language = ' . $db->quote($language),
                array('filter_content_' . $index => $whereExpr)
            );

        if ($whereValue && self::SKIP_WHERE !== $whereValue) {
            if (is_array($whereValue)) {
                $select->where($whereExpr . ' IN (?)', $whereValue);
            } else {
                $select->where($whereExpr . ' = ?', $whereValue);
            }
        }
    }

    public function _joinConnection(
        \Zend_Db_Select $select,
        $index,
        $origin,
        $type,
        $whereValue)
    {
        if (!strlen($whereValue)) {
            return;
        }

        if ('target' === $origin) {
            $source = 'source';
            $target = 'target';
        } elseif ('source' === $origin) {
            $source = 'target';
            $target = 'source';
        } else {
            throw new OutOfBoundsException(
                'Origin must be "source" or "target" ' . $origin . ' given.'
            );
        }

        $db = $select->getAdapter();

        $filterEtc = 'filter_etc_' . $index;
        $qFilterEtc = $db->quoteIdentifier($filterEtc);

        $select->join(
            array($filterEtc => $db->prefix . 'element_tree_connections'),
            "$qFilterEtc.$source = ch.tid AND $qFilterEtc.type = " . $db->quote($type),
            array('filter_content_' . $index => $target)
        );

        if (self::SKIP_WHERE !== $whereValue) {
            $select->where("$filterEtc.$target = ?", $whereValue);
        }
    }

    /**
     * Get raw values from select.
     *
     * @param string $filter
     *
     * @return array <value> => <counter>
     */
    protected function _getRawFilterOptions($filter, $restAll)
    {
        static $cache = array();

        // backup filter values
        $origFilterValues = $this->_catch->getFilterValues();

        $filterValues = $restAll
            ? array()
            : $origFilterValues;

        $filterValues[$filter] = self::SKIP_WHERE;

        $this->_catch->setFilterValues($filterValues);

        $contentFilter = 'filter_content_' . $filter;

        $select = $this->_catch->createReducedSelect();
        $origColumns = $select->getPart(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::GROUP)
            ->reset(Zend_Db_Select::COLUMNS)
            ->reset(Zend_Db_Select::ORDER)
            ->group($contentFilter);

        foreach ($origColumns as $column) {
            if ($contentFilter === $column[2]) {
                $select->columns(array($column[2] => $column[1]), $column[0]);
            }
        }

        $select->columns(array('counter' => new Zend_Db_Expr('count(ch.eid)')));

        $cacheId = md5((string) $select);
        if (!isset($cache[$cacheId])) {
            $db = $select->getAdapter();
            $cache[$cacheId] = $db->fetchPairs($select);
        }

        // reset filter values
        $this->_catch->setFilterValues($origFilterValues);

        return $cache[$cacheId];
    }

}

