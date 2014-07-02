<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle;

use Phlexible\Bundle\DataSourceBundle\Job\CleanupJob;
use Phlexible\Bundle\QueueBundle\Queue\QueueItem;

/**
 * Meta set item
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Item
{
    /**
     * @var string
     */
    protected $_setId = null;

    /**
     * @var ItemIdentifier
     */
    protected $_identifier = null;

    /**
     * @var array
     */
    protected $_keys = array();

    /**
     * @var array
     */
    protected $_options = array();

    /**
     * @var array
     */
    protected $_synchronized = array();

    /**
     * @var array
     */
    protected $_required = array();

    /**
     * @var array
     */
    protected $_readonly = array();

    /**
     * @var array
     */
    protected $_values = array();

    /**
     * @var array
     */
    protected $_types = array();

    /**
     * @var bool
     */
    protected $empty = true;

    /**
     * @param string        $setId
     * @param ItemInterface $itemIdentifier
     */
    public function __construct($setId, ItemInterface $itemIdentifier)
    {
        $this->_load($setId, $itemIdentifier);
    }

    /**
     * Is this metaset item empty?
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->empty;
    }

    /**
     * Magic value getter
     *
     * @param string $key
     * @return string
     * @throws \InvalidArgumentException
     */
    public function __get($key)
    {
        if (!array_key_exists($key, $this->_values))
        {
            throw new \InvalidArgumentException('Key "'.$key.'" not allowed.');
        }

        return $this->_values[$key];
    }

    /**
     * Magic value setter
     *
     * @param string $key
     * @param string $value
     * @throws \InvalidArgumentException
     */
    public function __set($key, $value)
    {
        if (!array_key_exists($key, $this->_values))
        {
            throw new \InvalidArgumentException('Key "'.$key.'" not allowed.');
        }

        $this->_values[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->_values[$key]);
    }

    /**
     * Return keys for this item
     *
     * @return array
     */
    public function getKeys()
    {
        return $this->_keys;
    }

    /**
     * Is this key allowed?
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasKey($key)
    {
        return in_array($key, $this->_keys);
    }

    /**
     * Return values for this item
     *
     * @return array
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Return single value for this item
     *
     * @param string $key
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getValue($key)
    {
        if (!isset($this->_values[$key]))
        {
            throw new \InvalidArgumentException('Key "'.$key.'" not allowed.');
        }

        return $this->_values[$key];
    }

    /**
     * Return types for this item
     *
     * @return string
     */
    public function getTypes()
    {
        return $this->_types;
    }

    /**
     * Return single type for this item
     *
     * @param string $key
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getType($key)
    {
        if (!array_key_exists($key, $this->_types))
        {
            throw new \InvalidArgumentException('Key "'.$key.'" not allowed.');
        }

        return $this->_types[$key];
    }

    /**
     * Return all options for this item
     *
     * @return string
     */
    public function getAllOptions()
    {
        return $this->_options;
    }

    /**
     * Return options for this item
     *
     * @param string $key
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getOptions($key)
    {
        if (!array_key_exists($key, $this->_options))
        {
            throw new \InvalidArgumentException('Key "'.$key.'" not allowed.');
        }

        return $this->_options[$key];
    }

    /**
     * Return array representation
     *
     * @param string $language
     * @return array
     */
    public function toArray($language)
    {
        $container = \MWF_Registry::getContainer();

        $dataSourceRepository = $container->dataSourcesRepository;

        $t9n        = $container->t9n;
        try {
            $pageKeys   = $t9n->{'metadata-keys'}->toArray();
        } catch (\Exception $e) {
            $pageKeys = array();
        }
        try {
            $pageSelect = $t9n->{'metadata-selectvalues'}->toArray();
        } catch (\Exception $e) {
            $pageSelect = array();
        }

        $data = array();

        foreach($this->_keys as $key)
        {
            $tkey         = $key;
            $type         = $this->_types[$key];
            $options      = $this->_options[$key];
            $value        = !empty($this->_values[$key]) ? $this->_values[$key] : '';
            $required     = false;
            $synchronized = false;
            $readonly     = false;

            if (!empty($pageKeys[$key]))
            {
                $tkey = $pageKeys[$key];
            }

            if ($type == 'select' && !empty($options))
            {
                $dummy   = explode(',', $options);
                $options = array();

                foreach ($dummy as $optionKey)
                {
                    $optionValue = $optionKey;
                    if (isset($pageSelect[$optionKey]))
                    {
                        $optionValue = $pageSelect[$optionKey];
                    }
                    $options[$optionKey] = $optionValue;
                }
            }
            elseif ($type == 'suggest' && !empty($options))
            {
                $sourceId = $options;

                $source     = $dataSourceRepository->getDataSourceById($sourceId, $language);
                $sourceKeys = $source->getKeys();

                $options = array(
                    'source_id' => $sourceId,
                    'values'    => array(),
                );

                foreach ($sourceKeys as $sourceKey)
                {
                    $sourceValue = $sourceKey;

                    $options['values'][] = array($sourceKey, $sourceValue);
                }
            }

            if (!empty($this->_required[$key]))
            {
                $required = true;
            }

            if (!empty($this->_synchronized[$key]))
            {
                $synchronized = true;
            }

            if (!empty($this->_readonly[$key]))
            {
            	$readonly = true;
            }

            $data[$key] = array(
                'key'          => $key,
                'tkey'         => $tkey,
                'type'         => $type,
                'options'      => $options,
                'value'        => $value,
                'required'     => $required,
                'synchronized' => $synchronized,
            	'readonly'     => $readonly,
            );
        }

        return $data;
    }

    /**
     * For suggest fields, data source must be cleaned up.
     */
    protected function _queueDataSourceCleanup()
    {
        foreach ($this->_types as $key => $type)
        {
            if ('suggest' !== $type)
            {
                continue;
            }

            \MWF_Registry::getContainer()->get('queue.service')->addUniqueJob(
                new CleanupJob(),
                QueueItem::PRIORITY_LOW
            );

            break;
        }
    }

    public function save()
    {
        $db = \MWF_Registry::getContainer()->dbPool->default;

        $baseData = array(
            'set_id'  => $this->_setId
        );

        $select = $db->select()
                     ->from($this->_identifier->getTableName())
                     ->where($db->quoteIdentifier('set_id') . ' = ?', $this->_setId)
                     ->where($db->quoteIdentifier($this->_identifier->getKeyField()) . ' = :key');

        $baseWhere = $db->quoteIdentifier('set_id') . ' = ' . $db->quote($this->_setId);
        foreach($this->_identifier->getIdentifiers() as $key => $value)
        {
            $baseWhere .=  ' AND ' . $db->quoteIdentifier($key) . ' = ' . $db->quote($value);
            $baseData[$key] = $value;

            $select->where($db->quoteIdentifier($key) . ' = ?', $value);
        }

        foreach($this->_values as $key => $value)
        {
//            if (!$value)
//            {
//                continue;
//            }

            $insertData = $baseData;
            $insertData[$this->_identifier->getKeyField()]   = $key;
            $insertData[$this->_identifier->getValueField()] = $value;

            $where = $baseWhere;
            $where .= ' AND ' . $db->quoteIdentifier($this->_identifier->getKeyField()) . ' = ' . $db->quote($key);

            if ($db->fetchRow($select, array('key' => $key)))
            {
                $db->update($this->_identifier->getTableName(), $insertData, $where);
            }
            else
            {
                $db->insert($this->_identifier->getTableName(), $insertData);
            }
        }

        $this->_queueDataSourceCleanup();
    }

    /**
     * Load item
     *
     * @param string        $setId
     * @param ItemInterface $itemIdentifier
     */
    protected function _load($setId, ItemInterface $itemIdentifier)
    {
        if ($setId instanceof MetaSet)
        {
            $setId = $setId->getId();
        }

        $db = \MWF_Registry::getContainer()->dbPool->default;

        $select = $db->select()
            ->from($itemIdentifier->getTableName(), array($itemIdentifier->getKeyField(), $itemIdentifier->getValueField()))
            ->where('set_id = ?', $setId);

        foreach($itemIdentifier->getIdentifiers() as $key => $value)
        {
            $select->where($db->quoteIdentifier($key) . ' = ?', $value);
        }

        $result = $db->fetchPairs($select);

        $metaSetRepository = \MWF_Registry::getContainer()->get('metasets.repository');
        $metaSet = $metaSetRepository->find($setId);

        $keys         = array();
        $values       = array();
        $types        = array();
        $options      = array();
        $synchronized = array();
        $required     = array();
        $readonly     = array();

        foreach ($metaSet->getKeys() as $row)
        {
            $keys[]                    = $row['key'];
            $values[$row['key']]       = !empty($result[$row['key']]) ? $result[$row['key']] : null;
            $types[$row['key']]        = $row['type'];
            $options[$row['key']]      = $row['options'];
            $synchronized[$row['key']] = $row['synchronized'];
            $required[$row['key']]     = $row['required'];
            $readonly[$row['key']]     = $row['readonly'];
        }

        $this->_setId        = $setId;
        $this->_identifier   = $itemIdentifier;
        $this->_keys         = $keys;
        $this->_values       = $values;
        $this->_types        = $types;
        $this->_options      = $options;
        $this->_synchronized = $synchronized;
        $this->_required     = $required;
        $this->_readonly     = $readonly;

        if (count($result))
        {
            $this->empty = false;
        }
    }
}
