<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\MetaData;

use Phlexible\Component\Database\ConnectionManager;
use Phlexible\Component\Identifier\Identifier;
use Phlexible\Bundle\MetaSetBundle\MetaSet\MetaSetInterface;

/**
 * Meta data repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaDataRepository
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $db;

    /**
     * @param ConnectionManager $connectionManager
     */
    public function __construct(ConnectionManager $connectionManager)
    {
        $this->db = $connectionManager->default;
    }

    /**
     * Load meta data
     *
     * @param MetaSetInterface $metaSet
     * @param array            $identifiers
     *
     * @return MetaDataInterface
     */
    public function load(MetaSetInterface $metaSet, array $identifiers)
    {
        $select = $this->db->select()
            ->from($this->db->prefix . 'media_file_meta')
            ->where('set_id = ?', $metaSet->getId());

        foreach ($identifiers as $identifier => $value) {
            $select->where($this->db->quoteIdentifier($identifier) . ' = ?', $value);
        }

        $rows = $this->db->fetchAll($select);

        $metaData = new MetaData();
        $metaData->setIdentifiers($identifiers);

        foreach ($rows as $row) {
            $metaData->set($row['meta_key'], $row['meta_value'], $row['meta_language']);
        }

        return $metaData;
    }

    /**
     * @param MetaDataInterface $metaData
     */
    public function save(MetaDataInterface $metaData)
    {
        $baseData = array(
            'set_id'     => $metaData->getId(),
            'identifier' => $metaData->getIdentifier(),
            'language'   => $metaData->getLanguage(),
        );

        foreach ($metaData->getMetaSet()->getFields() as $field) {
            $insertData = $baseData;

            $insertData['key'] = $field->getKey();
            $insertData['value'] = $metaData->get($field->getKey());

            $this->db->insert(
                $this->db->prefix . 'metasets_data',
                $insertData
            );
        }

        $this->_queueDataSourceCleanup();
    }
}
