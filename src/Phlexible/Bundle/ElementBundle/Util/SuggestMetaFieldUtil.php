<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Util;

use Phlexible\Component\Database\ConnectionManager;

/**
 * Utility class for suggest meta fields.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SuggestMetaFieldUtil
{
    /**
     * Database connection pool.
     *
     * @var ConnectionManager
     */
    protected $_dbPool;

    /**
     * @var string
     */
    protected $_seperatorChar;

    /**
     * @param ConnectionManager $dbPool
     * @param string            $seperatorChar
     */
    public function __construct(ConnectionManager $dbPool, $seperatorChar)
    {
        $this->_dbPool        = $dbPool;
        $this->_seperatorChar = $seperatorChar;
    }

    /**
     * Fetch all data source values used in any element versions.
     *
     * @param $dataSourceId
     * @param $language
     */
    public function fetchUsedValues($dataSourceId, $language)
    {
        $db = $this->_dbPool->read;

        $sql = $db->select()
                  ->distinct()

                  // used to limit on element type version
                  ->from(
                      array('ev' => $db->prefix . 'element_version'),
                      array()
                  )

                  // connection to content table element data language
                  ->join(
                      array('evmi' => $db->prefix . 'element_version_metaset_items'),
                      'evmi.eid = ev.eid AND ' .
                      'evmi.version = ev.version AND ' .
                      'evmi.language = :language',
                      array('value')
                  )

                  // used to limit on suggest fields and data source id
                  ->join(
                      array('msk' => $db->prefix . 'meta_set_keys'),
                      'msk.set_id = evmi.set_id AND ' .
                      'msk.key = evmi.key AND ' .
                      'msk.type = :field_type AND ' .
                      'msk.options = :datasource_id',
                      array()
                  );

        $bind = array(
            ':language'      => $language,
            ':field_type'    => 'suggest',
            ':datasource_id' => $dataSourceId,
        );

        // fetch suggest field content
        $result = $db->fetchCol($sql, $bind);

        $uniqueKeys = $this->splitSuggestValues($result);

        return $uniqueKeys;
    }

    /**
     * Fetch all data source values used in element online versions.
     *
     * @param $dataSourceId
     * @param $language
     */
    public function fetchOnlineValues($dataSourceId, $language)
    {
        $db = $this->_dbPool->read;

        $sql = $db->select()
                  ->distinct()

                  // fetch only online element versions
                  ->from(
                      array('eto' => $db->prefix . 'element_tree_online'),
                      array()
                  )

                  // used to limit on element type version
                  ->join(
                      array('ev' => $db->prefix . 'element_version'),
                      'eto.eid = ev.eid AND eto.version = ev.version',
                      array()
                  )

                  // connection to content table element data language
                  ->join(
                      array('evmi' => $db->prefix . 'element_version_metaset_items'),
                      'evmi.eid = ev.eid AND ' .
                      'evmi.version = ev.version AND ' .
                      'evmi.language = :language',
                      array('value')
                  )

                  // used to limit on suggest fields and data source id
                  ->join(
                      array('msk' => $db->prefix . 'meta_set_keys'),
                      'msk.set_id = evmi.set_id AND ' .
                      'msk.key = evmi.key AND ' .
                      'msk.type = :field_type AND ' .
                      'msk.options = :datasource_id',
                      array()
                  );

        $bind = array(
            ':language'      => $language,
            ':field_type'    => 'suggest',
            ':datasource_id' => $dataSourceId,
        );

        // fetch suggest field content
        $result = $db->fetchCol($sql, $bind);

        $uniqueKeys = $this->splitSuggestValues($result);

        return $uniqueKeys;
    }

    /**
     * Split list of suggest values into pieces and remove duplicates.
     *
     * @param array $concatenated
     *
     * @return array
     */
    public function splitSuggestValues(array $concatenated)
    {
        $keys = array();
        foreach ($concatenated as $value)
        {
            $splitted = explode($this->_seperatorChar, $value);
            foreach ($splitted as $key)
            {
                $key = trim($key);

                // skip empty values
                if (strlen($key))
                {
                    $keys[] = $key;
                }
            }
        }

        $uniqueKeys = array_unique($keys);

        return $uniqueKeys;
    }
}
