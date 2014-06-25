<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Usage;

use Phlexible\Component\Database\ConnectionManager;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderInterface;

/**
 * Folder usage repository
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class FolderUsageRepository
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @param ConnectionManager $connectionManager
     */
    public function __construct(ConnectionManager $connectionManager)
    {
        $this->db = $connectionManager->default;
    }

    /**
     * Find usage by folder
     *
     * @param FolderInterface $folder
     *
     * @return FolderUsage[]
     */
    public function findByFolder(FolderInterface $folder)
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'media_folder_usage')
            ->where('folder_id = ?', $folder->getId());

        $rows = $this->db->fetchAll($select);

        return $this->mapRows($rows);
    }

    /**
     * {@inheritdoc}
     */
    public function getByEid($eid, $status = null)
    {
        $select = $this->db
            ->select()
            ->from(
                $this->db->prefix . 'media_folder_usage',
                array('folder_id', 'usage_id', 'usage_type', 'status')
            )
            ->where($this->db->quoteIdentifier('usage_id') . ' = ?', (integer) $eid);

        // filter by status if parameter is specified
        if ($status) {
            $select->where($this->db->quoteIdentifier('status') . ' & ?', (integer) $status);
        }

        $result = $this->db->fetchAll($select);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getByType($type)
    {
        $select = $this->db
            ->select()
            ->from(
                $this->db->prefix . 'media_folder_usage',
                array('folder_id', 'usage_id', 'usage_type', 'status')
            )
            ->where('usage_type = ?', $type);

        $result = $this->db->fetchAll($select);

        return $result;
    }

    /**
     * Save usage
     *
     * @param FolderUsage $usage
     * @param boolean     $update
     */
    public function save(FolderUsage $usage, $update = false)
    {
        if (strlen($usage->getUsageId()) && strlen($usage->getFolderId()) && $update) {
            $this->db->update(
                $this->db->prefix . 'media_folder_usage',
                array(
                    'folder_id'  => $usage->getFolderId(),
                    'usage_type' => $usage->getUsageType(),
                    'usage_id'   => $usage->getUsageId(),
                    'status'     => $usage->getStatus(),
                ),
                array(
                    'folder_id = ?' => $usage->getFolderId(),
                    'usage_id = ?'  => $usage->getUsageId(),
                )
            );

        } else {
            $this->db->insert(
                $this->db->prefix . 'media_folder_usage',
                array(
                    'folder_id'  => $usage->getFolderId(),
                    'usage_type' => $usage->getUsageType(),
                    'usage_id'   => $usage->getUsageId(),
                    'status'     => $usage->getStatus(),
                )
            );
        }
    }

    /**
     * Delete by folder
     *
     * @param FolderInterface $folder
     */
    public function deleteByFolder(FolderInterface $folder)
    {
        $this->db->delete(
            $this->db->prefix . 'media_folder_usage',
            array(
                'folder_id = ?' => $folder->getId
            )
        );
    }

    /**
     * @param array $rows
     *
     * @return FolderUsage[]
     */
    private function mapRows(array $rows)
    {
        $usages = array();
        foreach ($rows as $row) {
            $usages[] = $this->mapRow($row);
        }

        return $usages;
    }

    /**
     * @param array $row
     *
     * @return FolderUsage
     */
    private function mapRow(array $row)
    {
        $usage = new FolderUsage(
            $row['folder_id'],
            $row['usage_type'],
            $row['usage_id'],
            $row['status']
        );

        return $usage;
    }
}