<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Exception.php 4161 2008-03-11 18:37:34Z swentz $
 */

namespace Phlexible\Bundle\ElementBundle\Element;

/**
 * Element history
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 */
class XElementHistory
{
    /**
     * Get range of history items
     *
     * @param int $eid
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public static function getRange($filter = null, $offset = 0, $limit = 0, $sort = 'create_time', $dir = 'DESC')
    {
        $db = MWF_Registry::getContainer()->dbPool->default;

        $innerSelect1 = $db->select()
            ->from(
                $db->prefix . 'element_history',
                array(
                    'type'      => new Zend_Db_Expr('"element"'),
                    'id',
                    'tid'       => new Zend_Db_Expr('null'),
                    'teaser_id' => new Zend_Db_Expr('null'),
                    'eid',
                    'version',
                    'language',
                    'action',
                    'comment',
                    'create_uid',
                    'create_time'
                )
            );

        $innerSelect2 = $db->select()
            ->from(
                $db->prefix . 'element_tree_history',
                array(
                    'type'      => new Zend_Db_Expr('"node"'),
                    'id',
                    'tid',
                    'teaser_id' => new Zend_Db_Expr('null'),
                    'eid',
                    'version',
                    'language',
                    'action',
                    'comment',
                    'create_uid',
                    'create_time'
                )
            );

        $innerSelect3 = $db->select()
            ->from(
                $db->prefix . 'element_tree_teasers_history',
                array(
                    'type' => new Zend_Db_Expr('"teaser"'),
                    'id',
                    'tid'  => 'teaser_id',
                    'teaser_id',
                    'eid',
                    'version',
                    'language',
                    'action',
                    'comment',
                    'create_uid',
                    'create_time'
                )
            );

        $innerSelect = $db->select()
            ->union(array($innerSelect1, $innerSelect2, $innerSelect3));

        $select = $db->select()
            ->from(
                array('t' => $innerSelect),
                array('type', 'id', 'tid', 'eid', 'version', 'language', 'action', 'comment', 'create_time')
            )
            ->join(array('u' => $db->prefix . 'user'), 't.create_uid = u.uid', 'username')
            ->order('t.' . $sort . ' ' . $dir)
            ->limit($limit, $offset);

        if (is_string($filter)) {
            $select->where('eid = ?', $filter);
        } elseif (is_array($filter)) {
            foreach ($filter as $key => $value) {
                if (!$value) {
                    continue;
                }

                if (in_array($key, array('action', 'comment'))) {
                    $select->where('t.' . $db->quoteIdentifier($key) . ' LIKE ?', '%' . $value . '%');
                } else {
                    $select->where('t.' . $db->quoteIdentifier($key) . ' = ?', $value);
                }
            }
        }

        $result = $db->fetchAll($select);

        return $result;
    }

    /**
     * Get all history items
     *
     * @param int $eid
     *
     * @return string
     */
    public static function getCount($filter = null)
    {
        $db = MWF_Registry::getContainer()->dbPool->default;

        $innerSelect1 = $db->select()
            ->from(
                $db->prefix . 'element_history',
                array(
                    'type'      => new Zend_Db_Expr('"element"'),
                    'id',
                    'tid'       => new Zend_Db_Expr('null'),
                    'teaser_id' => new Zend_Db_Expr('null'),
                    'eid',
                    'version',
                    'language',
                    'action',
                    'comment',
                    'create_uid',
                    'create_time'
                )
            );

        $innerSelect2 = $db->select()
            ->from(
                $db->prefix . 'element_tree_history',
                array(
                    'type'      => new Zend_Db_Expr('"node"'),
                    'id',
                    'tid',
                    'teaser_id' => new Zend_Db_Expr('null'),
                    'eid',
                    'version',
                    'language',
                    'action',
                    'comment',
                    'create_uid',
                    'create_time'
                )
            );

        $innerSelect3 = $db->select()
            ->from(
                $db->prefix . 'element_tree_teasers_history',
                array(
                    'type' => new Zend_Db_Expr('"teaser"'),
                    'id',
                    'tid'  => 'teaser_id',
                    'teaser_id',
                    'eid',
                    'version',
                    'language',
                    'action',
                    'comment',
                    'create_uid',
                    'create_time'
                )
            );

        $innerSelect = $db->select()
            ->union(array($innerSelect1, $innerSelect2, $innerSelect3));

        $select = $db->select()
            ->from(array('t' => $innerSelect), new Zend_Db_Expr('COUNT(t.eid)'));

        if (is_string($filter)) {
            $select->where('eid = ?', $filter);
        } elseif (is_array($filter)) {
            foreach ($filter as $key => $value) {
                if (!$value) {
                    continue;
                }

                if (in_array($key, array('action', 'comment'))) {
                    $select->where('t.' . $db->quoteIdentifier($key) . ' LIKE ?', '%' . $value . '%');
                } else {
                    $select->where('t.' . $db->quoteIdentifier($key) . ' = ?', $value);
                }
            }
        }

        $result = $db->fetchOne($select);

        return $result;
    }

    /**
     * Check if an element is saved in a given language.
     *
     * @param int    $eid
     * @param string $language
     *
     * @return int|null First version saved in this language.
     */
    public static function getFirstVersionByEidAndLanguage($eid, $language)
    {
        self::_loadCache();

        if (!isset(self::$_cache[$eid][$language][0])) {
            return null;
        }

        return (int) self::$_cache[$eid][$language][0]['min_version'];
    }

    protected static $_cache = null;

    /**
     * Get all languages saved for an element.
     *
     * @param int $eid
     *
     * @return array
     */
    public static function getSavedLanguagesByEid($eid)
    {
        self::_loadCache();

        if (!isset(self::$_cache[$eid])) {
            return array();
        }

        return array_keys(self::$_cache[$eid]);
    }

    protected static function _loadCache()
    {
        if (null !== self::$_cache) {
            return;
        }

        $db = MWF_Registry::getContainer()->dbPool->default;

        $minVersionExpr = new Zend_Db_Expr('MIN(' . $db->quoteIdentifier('version') . ')');

        $select = $db->select()
            ->distinct()
            ->from(
                $db->prefix . 'element_history',
                array('eid', 'language', 'min_version' => $minVersionExpr)
            )
            ->where('language IS NOT NULL')
            //->where('action LIKE ?', 'save%')
            ->where('action LIKE ' . $db->quote('save%') . ' OR comment LIKE ' . $db->quote('New translation for%'))
            ->group(array('eid', 'language'));

        $result = $db->fetchAll($select);

        $result = Brainbits_Util_Array::groupBy($result, array('eid', 'language'));

        self::$_cache = $result;
    }
}
