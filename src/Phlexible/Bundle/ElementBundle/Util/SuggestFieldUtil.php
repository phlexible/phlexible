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
 * Utility class for suggest fields.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SuggestFieldUtil
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

                  // used to limit on suggest fields and data source id
                  ->join(
                      array('ets' => $db->prefix . 'elementtype_structure'),
                      'ets.element_type_id = ev.element_type_id AND ' .
                      'ets.version = ev.element_type_version',
                      array()
                  )

                  // connection to content table element data language
                  ->join(
                      array('ed' => $db->prefix . 'element_data'),
                      'ed.ds_id = ets.ds_id AND ' .
                      'ev.eid = ed.eid AND ev.version = ed.version',
                      array()
                  )

                  // fetch content
                  ->join(
                      array('edl' => $db->prefix . 'element_data_language'),
                      'ed.eid = edl.eid AND ed.version = edl.version AND ed.data_id = edl.data_id',
                      array('content')
                  )

                  // limit on suggest fields
                  ->where('ets.field_type = :field_type')

                  // limit on suggest fields
                  ->where('edl.language = :language')

                  // limit on data source
                  ->where('ets.options LIKE :options');

        $bind = array(
            ':language'   => $language,
            ':field_type' => 'suggest',
            ':options'    => '%s:13:"source_source";s:36:"' . $dataSourceId . '"%',
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
            $splitted  = explode($this->_seperatorChar, $value);
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

                  // used to limit on suggest fields and data source id
                  ->join(
                      array('ets' => $db->prefix . 'elementtype_structure'),
                      'ets.element_type_id = ev.element_type_id AND ' .
                      'ets.version = ev.element_type_version',
                      array()
                  )

                  // connection to content table element data language
                  ->join(
                      array('ed' => $db->prefix . 'element_data'),
                      'ed.ds_id = ets.ds_id AND ' .
                      'eto.eid = ed.eid AND eto.version = ed.version',
                      array()
                  )

                  // fetch content
                  ->join(
                      array('edl' => $db->prefix . 'element_data_language'),
                      'ed.eid = edl.eid AND ed.version = edl.version AND ed.data_id = edl.data_id AND eto.language = edl.language',
                      array('content')
                  )

                  // limit on suggest fields
                  ->where('eto.language = :language')

                  // limit on suggest fields
                  ->where('ets.field_type = :field_type')

                  // limit on data source
                  ->where('ets.options LIKE :options');

        $bind = array(
            ':language'   => $language,
            ':field_type' => 'suggest',
            ':options'    => '%s:13:"source_source";s:36:"' . $dataSourceId . '"%',
        );

        // fetch suggest field content
        $result = $db->fetchCol($sql, $bind);

        $uniqueKeys = $this->splitSuggestValues($result);

        return $uniqueKeys;
    }
}
