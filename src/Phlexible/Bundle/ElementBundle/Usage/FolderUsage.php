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
class FolderUsage
{
    const STATUS_ONLINE = 8;
    const STATUS_LATEST = 4;
    const STATUS_OLD    = 2;
    const STATUS_DEAD   = 1;

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

        if ($eid !== null)
        {
            $eids = array($eid);
        }
        else
        {
            $select = $db->select()
                ->from($db->prefix . 'element', 'eid');

            $eids = $db->fetchCol($select);
        }

        $cntInsert = 0;
        $cntDelete = 0;

        $types = array('folder');
        foreach ($types as $key => $type)
        {
            $types[$key] = $db->quote($type);
        }
        $typeString = implode(',', $types);

        $query =
#INSERT INTO
#    ' . $db->prefix . 'mediamanager_files_usage
            'SELECT DISTINCT
    edl.content AS folder_id,
    "element" AS usage_type,
    ed.eid AS usage_id,
    ed.version,
    edl.language
FROM
    ' . $db->prefix . 'elementtype_structure ets,
    ' . $db->prefix . 'element_data ed,
    ' . $db->prefix . 'element_data_language edl,
    ' . $db->prefix . 'mediamanager_folders mfo
WHERE
    ets.field_type IN ('.$typeString.')
AND
    ets.ds_id = ed.ds_id
AND
    ed.eid = :eid
AND
    ed.eid = edl.eid
AND
    ed.version = edl.version
AND
    ed.data_id = edl.data_id
AND
    edl.content IS NOT NULL
AND
    edl.content != ""
AND
    edl.content = mfo.id
';

        $select1 = $db->select()->from($db->prefix . 'element', 'latest_version')->where('eid = :eid');
        $select2 = $db->select()->from($db->prefix . 'element_tree_online', new Zend_Db_Expr('language||"_"||version'))->where('eid = :eid');
        $select3 = $db->select()->from($db->prefix . 'element_tree_teasers_online', new Zend_Db_Expr('language||"_"||version'))->where('eid = :eid');

        $select4 = $db->select()->from($db->prefix . 'element_tree', new Zend_Db_Expr('COUNT(*)'))->where('eid = :eid');
        $select5 = $db->select()->from($db->prefix . 'element_tree_teasers', new Zend_Db_Expr('COUNT(*)'))->where('teaser_eid = :eid');

        $log = '';

        foreach ($eids as $eid)
        {
            #$db->beginTransaction();

            $cntDelete += $db->delete(
                $db->prefix . 'mediamanager_folders_usage',
                array(
                    'usage_id = ?'   => $eid,
                    'usage_type = ?' => 'element',
                )
            );

            $rows = $db->fetchAll($query, array('eid' => (integer) $eid));
            $log .= 'Tree Online Versions: '; $log .= print_r($rows, true);

            if (!$rows)
            {
                continue;
            }

            $latestVersion        = $db->fetchOne($select1, array('eid' => $eid));
            $treeOnlineVersions   = $db->fetchCol($select2, array('eid' => $eid));
            $teaserOnlineVersions = $db->fetchCol($select3, array('eid' => $eid));
            $log .= 'Latest Version: ' . $latestVersion . PHP_EOL;
            $log .= 'Tree Online Versions: '; $log .= print_r($treeOnlineVersions, true);
            $log .= 'Teaser Online Versions: '; $log .= print_r($teaserOnlineVersions, true);

            $treeUsed    = $db->fetchOne($select4, array('eid' => $eid));
            $teasersUsed = $db->fetchOne($select5, array('eid' => $eid));

            #echo $latestVersion." ";
            #echo implode(',', $treeOnlineVersions)." ";
            #echo implode(',', $teaserOnlineVersions).PHP_EOL;

            $status = array();
            $insertRows = array();

            foreach ($rows as $row)
            {
                #echo $row['version'].PHP_EOL;
                $key = $row['folder_id'];
                $logPrefix = $key . ' ' . $row['eid'] . '_' . $row['language'] . '_' . $row['version'];

                if (!array_key_exists($key, $insertRows))
                {
                    $insertRows[$key] = $row;
                    $insertRows[$key]['status'] = 0;
                    unset($insertRows[$key]['version']);
                    unset($insertRows[$key]['language']);
                }

                if (in_array($row['language'] . '_' . $row['version'], $treeOnlineVersions) ||
                    in_array($row['language'] . '_' . $row['version'], $teaserOnlineVersions))
                {
                    $log .= $logPrefix . ': Online'.PHP_EOL;
                    $insertRows[$key]['status'] |= self::STATUS_ONLINE;
                }
                elseif ($row['version'] == $latestVersion)
                {
                    if ($treeUsed || $teasersUsed)
                    {
                        $log .= $logPrefix . ': Latest'.PHP_EOL;
                        $insertRows[$key]['status'] |= self::STATUS_LATEST;
                    }
                    else
                    {
                        $log .= $logPrefix . ': Latest, Dead'.PHP_EOL;
                        $insertRows[$key]['status'] |= self::STATUS_DEAD;
                    }
                }
                else
                {
                    if ($treeUsed || $teasersUsed)
                    {
                        $log .= $logPrefix . ': Old'.PHP_EOL;
                        $insertRows[$key]['status'] |= self::STATUS_OLD;
                    }
                    else
                    {
                        $log .= $logPrefix . ': Old, Dead'.PHP_EOL;
                        $insertRows[$key]['status'] |= self::STATUS_DEAD;
                    }
                }
            }

            //unset($insertRow['version']);
            //$insertRow['status'] = $status;

            foreach ($insertRows as $insertRow)
            {
                try
                {
                    $cntInsert += $db->insert(
                        $db->prefix . 'mediamanager_folders_usage',
                        $insertRow
                    );
                }
                catch (Exception $e)
                {
                    die($e->getMessage().PHP_EOL.$e->getTraceAsString());
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
     * @param string  $folderId
     * @param integer $status (optional)
     *
     * @return array
     */
    public function getAllByFolderId($folderId, $status = null)
    {
        $db = $this->_dbPool->read;

        $select = $db->select()
            ->from($db->prefix . 'mediamanager_folders_usage')
            ->where($db->quoteIdentifier('folder_id') . ' = ?', $folderId)
            ->where($db->quoteIdentifier('usage_type') . ' = ?', 'element');

        // filter by status if parameter is specified
        if ($status)
        {
            $select->where($db->quoteIdentifier('status') . ' & ?', (integer) $status);
        }

        $result = $db->fetchAll($select);

        return $result;
    }

    /**
     * Get all file usages for an element.
     *
     * @param integer $eid
     * @param integer $status (optional)
     *
     * @return array
     */
    public function getAllByEid($eid, $status = null)
    {
        $db = $this->_dbPool->read;

        $select = $db->select()
            ->from(
                $db->prefix . 'mediamanager_folders_usage',
                array('folder_id', 'usage_id', 'usage_type', 'status')
            )
            ->where($db->quoteIdentifier('usage_id') . ' = ?', (integer) $eid)
            ->where($db->quoteIdentifier('usage_type') . ' = ?', 'element');

        // filter by status if parameter is specified
        if ($status)
        {
            $select->where($db->quoteIdentifier('status') . ' & ?', (integer) $status);
        }

        $result = $db->fetchAll($select);

        return $result;
    }

    /**
     * Get all folder usages by status.
     *
     * @param integer $status
     *
     * @return array
     */
    public function getAllByStatus($status)
    {
        $db = $this->_dbPool->read;

        $select = $db->select()
            ->from(
                $db->prefix . 'mediamanager_folders_usage',
                array('folder_id', 'usage_id', 'usage_type', 'status')
            )
            ->where($db->quoteIdentifier('status') . ' & ?', (integer) $status)
            ->where($db->quoteIdentifier('usage_type') . ' = ?', 'element');

        $result = $db->fetchAll($select);

        return $result;
    }
}
