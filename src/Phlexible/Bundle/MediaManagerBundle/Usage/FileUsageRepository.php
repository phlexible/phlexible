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

/**
 * File usage repository
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class FileUsageRepository
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
     * Find usage by file
     *
     * @param FileInterface $file
     *
     * @return FileUsage[]
     */
    public function findByFile(FileInterface $file)
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'media_file_usage')
            ->where('file_id = ?', $file->getId());

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
                $this->db->prefix . 'media_file_usage',
                array('file_id', 'file_version', 'usage_id', 'usage_type', 'status')
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
                $this->db->prefix . 'media_file_usage',
                array('file_id', 'file_version', 'usage_id', 'usage_type', 'status')
            )
            ->where('usage_type = ?', $type);

        $result = $this->db->fetchAll($select);

        return $result;
    }

    /**
     * Save usage
     *
     * @param FileUsage $usage
     * @param boolean   $update
     */
    public function save(FileUsage $usage, $update = false)
    {
        if (strlen($usage->getUsageId()) && strlen($usage->getFileId()) && $update) {
            $this->db->update(
                $this->db->prefix . 'media_file_usage',
                array(
                    'file_id'      => $usage->getFileId(),
                    'file_version' => $usage->getFileVersion(),
                    'usage_type'   => $usage->getUsageType(),
                    'usage_id'     => $usage->getUsageId(),
                    'status'       => $usage->getStatus(),
                ),
                array(
                    'file_id = ?'  => $usage->getFileId(),
                    'usage_id = ?' => $usage->getUsageId(),
                )
            );

        } else {
            $this->db->insert(
                $this->db->prefix . 'media_file_usage',
                array(
                    'file_id'      => $usage->getFileId(),
                    'file_version' => $usage->getFileVersion(),
                    'usage_type'   => $usage->getUsageType(),
                    'usage_id'     => $usage->getUsageId(),
                    'status'       => $usage->getStatus(),
                )
            );
        }
    }

    /**
     * Delete by file
     *
     * @param FileInterface $file
     */
    public function deleteByFile(FileInterface $file)
    {
        $this->db->delete(
            $this->db->prefix . 'media_file_usage',
            array(
                'file_id = ?' => $file->getId()
            )
        );
    }

    /**
     * @param array $rows
     *
     * @return FileUsage[]
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
     * @return FileUsage
     */
    private function mapRow(array $row)
    {
        $fileUsage = new FileUsage(
            $row['file_id'],
            $row['file_version'],
            $row['usage_type'],
            $row['usage_id'],
            $row['status']
        );

        return $fileUsage;
    }
}