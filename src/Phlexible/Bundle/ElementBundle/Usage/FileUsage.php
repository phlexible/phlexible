<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Usage;

/**
 * File usage
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileUsage
{
    const STATUS_ONLINE = 8;
    const STATUS_LATEST = 4;
    const STATUS_OLD = 2;
    const STATUS_DEAD = 1;

    /**
     * @var MWF_Db_Pool
     */
    protected $_dbPool = null;

    /**
     * Constructor
     *
     * @param MWF_Db_Pool $dbPool
     */
    public function __construct(MWF_Db_Pool $dbPool)
    {
        $this->_dbPool = $dbPool;
    }

    public function update($eid = null)
    {
        $db = $this->_dbPool->write;

        if ($eid !== null) {
            $eids = array($eid);
        } else {
            $select = $db->select()
                ->from($db->prefix . 'element', 'eid');

            $eids = $db->fetchCol($select);
        }

        $cntInsert = 0;
        $cntDelete = 0;

        $types = array('image', 'video', 'flash', 'download');
        foreach ($types as $key => $type) {
            $types[$key] = $db->quote($type);
        }
        $typeString = implode(',', $types);

        $query =
            #INSERT INTO
            #    ' . $db->prefix . 'mediamanager_files_usage
            'SELECT DISTINCT
                IF(
                    LOCATE(";", edl.content),
                    LEFT(edl.content, 36),
                    edl.content
                ) AS file_id,
                IF(
                    LOCATE(";", edl.content),
                    MID(edl.content, 38),
                    1
                ) AS file_version,
                "element" AS usage_type,
                ed.eid AS usage_id' . /* , CONCAT(edl.eid,"_", edl.language, "_", ed.version) */
            ',
               ed.version,
               edl.language
           FROM
               ' . $db->prefix . 'element_data ed,
    ' . $db->prefix . 'element_data_language edl
WHERE
    ed.ds_id IN (
         SELECT DISTINCT ds_id
          FROM elementtype_structure
          WHERE field_type IN (' . $typeString . ')
    )
AND
    ed.eid = :eid
AND
    ed.eid = edl.eid AND ed.version = edl.version AND ed.data_id = edl.data_id
AND
    edl.content IS NOT NULL
AND
    edl.content != ""
AND (
    edl.content IN (SELECT DISTINCT id FROM ' . $db->prefix . 'mediamanager_files)
OR
    edl.content IN (SELECT DISTINCT CONCAT(id, ";", version) FROM ' . $db->prefix . 'mediamanager_files)
)';
        $select1 = $db->select()->from($db->prefix . 'element', 'latest_version')->where('eid = :eid');
        $select2 = $db->select()->from($db->prefix . 'element_tree_online', new Zend_Db_Expr('language||"_"||version'))
            ->where('eid = :eid');
        $select3 = $db->select()->from(
            $db->prefix . 'element_tree_teasers_online',
            new Zend_Db_Expr('language||"_"||version')
        )->where('eid = :eid');

        $select4 = $db->select()->from($db->prefix . 'element_tree', new Zend_Db_Expr('COUNT(*)'))->where('eid = :eid');
        $select5 = $db->select()->from($db->prefix . 'element_tree_teasers', new Zend_Db_Expr('COUNT(*)'))->where(
            'teaser_eid = :eid'
        );

        $log = '';

        foreach ($eids as $eid) {
            #$db->beginTransaction();

            $cntDelete += $db->delete(
                $db->prefix . 'mediamanager_files_usage',
                array(
                    'usage_id = ?'   => $eid,
                    'usage_type = ?' => 'element',
                )
            );

            $rows = $db->fetchAll($query, array('eid' => (int) $eid));

            if (!$rows) {
                continue;
            }

            $latestVersion = $db->fetchOne($select1, array('eid' => $eid));
            $treeOnlineVersions = $db->fetchCol($select2, array('eid' => $eid));
            $teaserOnlineVersions = $db->fetchCol($select3, array('eid' => $eid));
            $log .= 'Latest Version: ' . $latestVersion . PHP_EOL;
            $log .= 'Tree Online Versions: ';
            $log .= print_r($treeOnlineVersions, true);
            $log .= 'Teaser Online Versions: ';
            $log .= print_r($teaserOnlineVersions, true);

            $treeUsed = $db->fetchOne($select4, array('eid' => $eid));
            $teasersUsed = $db->fetchOne($select5, array('eid' => $eid));

            #echo $latestVersion." ";
            #echo implode(',', $treeOnlineVersions)." ";
            #echo implode(',', $teaserOnlineVersions).PHP_EOL;

            $status = array();
            $insertRows = array();

            foreach ($rows as $row) {
                #echo $row['version'].PHP_EOL;
                $key = $row['file_id'] . '_' . $row['file_version'];
                $logPrefix = $key . ' ' . $eid . '_' . $row['version'] . '_' . $row['language'];


                if (!array_key_exists($key, $insertRows)) {
                    $insertRows[$key] = $row;
                    $insertRows[$key]['status'] = 0;
                    unset($insertRows[$key]['version']);
                    unset($insertRows[$key]['language']);
                }

                if (in_array($row['language'] . '_' . $row['version'], $treeOnlineVersions) ||
                    in_array($row['language'] . '_' . $row['version'], $teaserOnlineVersions)
                ) {
                    $log .= $logPrefix . ': Online' . PHP_EOL;
                    $insertRows[$key]['status'] |= self::STATUS_ONLINE;
                } elseif ($row['version'] == $latestVersion) {
                    if ($treeUsed || $teasersUsed) {
                        $log .= $logPrefix . ': Latest' . PHP_EOL;
                        $insertRows[$key]['status'] |= self::STATUS_LATEST;
                    } else {
                        $log .= $logPrefix . ': Latest, Dead' . PHP_EOL;
                        $insertRows[$key]['status'] |= self::STATUS_DEAD;
                    }
                } else {
                    if ($treeUsed || $teasersUsed) {
                        $log .= $logPrefix . ': Old' . PHP_EOL;
                        $insertRows[$key]['status'] |= self::STATUS_OLD;
                    } else {
                        $log .= $logPrefix . ': Old, Dead' . PHP_EOL;
                        $insertRows[$key]['status'] |= self::STATUS_DEAD;
                    }
                }
            }

            //unset($insertRow['version']);
            //$insertRow['status'] = $status;

            foreach ($insertRows as $insertRow) {
                try {
                    $result = $db->insert($db->prefix . 'mediamanager_files_usage', $insertRow);
                    $cntInsert += $result;
                } catch (Exception $e) {
                    die($e->getMessage() . PHP_EOL . $e->getTraceAsString());
                }
            }

            #$db->commit();
        }

        return array(
            'delete' => $cntDelete,
            'insert' => $cntInsert,
            'log'    => $log,
        );
    }

    /**
     * Get all element usages for a file.
     *
     * @param string $fileId
     * @param int    $version
     * @param int    $status
     *
     * @return array
     */
    public function getAllByFileId($fileId, $version = null, $status = null)
    {
        $db = $this->_dbPool->read;

        $select = $db->select()
            ->from($db->prefix . 'mediamanager_files_usage')
            ->where($db->quoteIdentifier('file_id') . ' = ?', $fileId)
            ->where($db->quoteIdentifier('usage_type') . ' = ?', 'element');

        // filter by file version if parameter is specified
        if ($version) {
            $select->where($db->quoteIdentifier('file_version') . ' = ?', (int) $version);
        }

        // filter by status if parameter is specified
        if ($status) {
            $select->where($db->quoteIdentifier('status') . ' & ?', (int) $status);
        }

        $result = $db->fetchAll($select);

        return $result;
    }

    /**
     * Get all file usages for an element.
     *
     * @param int $eid
     * @param int $status
     */
    public function getAllByEid($eid, $status = null)
    {
        $db = $this->_dbPool->read;

        $select = $db->select()
            ->from(
                $db->prefix . 'mediamanager_files_usage',
                array('file_id', 'file_version', 'usage_id', 'usage_type', 'status')
            )
            ->where($db->quoteIdentifier('usage_id') . ' = ?', (int) $eid)
            ->where($db->quoteIdentifier('usage_type') . ' = ?', 'element');

        // filter by status if parameter is specified
        if ($status) {
            $select->where($db->quoteIdentifier('status') . ' & ?', (int) $status);
        }

        $result = $db->fetchAll($select);

        return $result;
    }
}
