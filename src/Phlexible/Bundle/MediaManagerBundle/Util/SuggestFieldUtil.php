<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Util;

use Phlexible\Component\Database\ConnectionManager;

/**
 * Utility class for suggest fields.
 *
 * @author Phillip Look <pl@brainbits.net>
 *
 * @see Phlexible\Bundle\DataSourceBundle\DataSource
 * @see Makeweb_Fields_Field_Suggest
 */
class SuggestFieldUtil
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @var string
     */
    private $seperatorChar;

    /**
     * @param ConnectionManager $dbPool
     * @param string            $seperatorChar
     */
    public function __construct(ConnectionManager $dbPool, $seperatorChar)
    {
        $this->db = $dbPool->default;
        $this->seperatorChar = $seperatorChar;
    }

    /**
     * Fetch all data source values used in any media file metaset.
     *
     * @param string $dataSourceId
     * @param array  $languages
     *
     * @return array
     */
    public function fetchUsedValues($dataSourceId, array $languages)
    {
        $valueSelects = array();
        foreach ($languages as $language)
        {
            $language = trim($language);

            $valueSelects[] = $this->db
                ->select()
                ->from(
                    array('msk' => $this->db->prefix . 'meta_set_keys'),
                    array()
                )
                ->join(
                    array('mfmi' => $this->db->prefix . 'mediamanager_files_metasets_items'),
                    'mfmi.meta_key = msk.' . $this->db->quoteIdentifier('key'),
                    array('meta_value_' . $language)
                )
                ->where('msk.type = ?', 'suggest')
                ->where('msk.options = ?', $dataSourceId);

            $valueSelects[] = $this->db
                ->select()
                ->from(
                    array('msk' => $this->db->prefix . 'meta_set_keys'),
                    array()
                )
                ->join(
                    array('mfomi' => $this->db->prefix . 'mediamanager_folder_metasets_items'),
                    'mfomi.meta_key = msk.' . $this->db->quoteIdentifier('key'),
                    array('meta_value_' . $language)
                )
                ->where('msk.type = ?', 'suggest')
                ->where('msk.options = ?', $dataSourceId);
        }

        $select = $this->db->select()->union($valueSelects);

        $result = $this->db->fetchCol($select);

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
            $splitted = explode($this->seperatorChar, $value);
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
