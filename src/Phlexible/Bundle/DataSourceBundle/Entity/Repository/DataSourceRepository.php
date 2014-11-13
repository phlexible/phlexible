<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\DataSource;

use Doctrine\ORM\EntityRepository;

/**
 * Data source repository
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class DataSourceRepository extends EntityRepository
{
    /**
     * Remove deleted values / insert added values.
     *
     * @param string $dataSourceId
     * @param string $language
     * @param array  $activeKeys
     * @param array  $inactiveKeys
     */
    protected function updateValues($dataSourceId, $language, array $activeKeys, array $inactiveKeys)
    {
        $oldKeys = $this->getAllValuesByDataSourceId($dataSourceId, $language);
        $allKeys = array_merge($activeKeys, $inactiveKeys);

        $oldKeysL = $this->lowerValue($oldKeys);

        // delete values removed from data source
        $this->deleteValues(
            $dataSourceId,
            array_keys(array_diff($oldKeysL, $this->lowerValue($allKeys)))
        );

        // insert values added to data source
        $this->insertValues(
            $dataSourceId,
            $language,
            array_diff_key($this->lowerValueAsKey($activeKeys), $this->lowerValueAsKey($oldKeys)),
            true
        );

        $this->insertValues(
            $dataSourceId,
            $language,
            array_diff_key($this->lowerValueAsKey($inactiveKeys), $this->lowerValueAsKey($oldKeys)),
            false
        );

        // fix existing values
        $this->fixValues(
            $dataSourceId,
            array_keys(array_intersect($oldKeysL, $this->lowerValue($activeKeys))),
            true
        );

        $this->fixValues(
            $dataSourceId,
            array_keys(array_intersect($oldKeysL, $this->lowerValue($inactiveKeys))),
            false
        );

    }

    /**
     * Fix active flag in existing keys.
     *
     * @param string $dataSourceId
     * @param array  $existingIds
     * @param bool   $isActive
     */
    protected function fixValues($dataSourceId, array $existingIds, $isActive)
    {
        if (!count($existingIds)) {
            return;
        }

        $this->db->update(
            $this->db->prefix . self::T_DATASOURCE_VALUE,
            array(
                self::C_DATASOURCE_VALUE_ACTIVE => (int) $isActive,
            ),
            array(
                self::C_DATASOURCE_VALUE_ID . ' in (?)'     => $existingIds,
                self::C_DATASOURCE_VALUE_SOURCE_ID . ' = ?' => $dataSourceId,
            )
        );
    }

    /**
     * Insert new values to an data source.
     *
     * @param string $dataSourceId
     * @param string $language
     * @param array  $insertedKeys
     * @param bool   $isActive
     *
     * @throws InvalidArgumentException if language not set correctly
     */
    protected function insertValues($dataSourceId, $language, array $insertedKeys, $isActive)
    {
        if (2 !== strlen($language)) {
            $msg = 'Language not set correctly. Found: ' . $language;
            throw new InvalidArgumentException($msg);
        }

        foreach ($insertedKeys as $insertedKey) {
            $hashableString = $dataSourceId . $insertedKey . $language;

            $this->db->insert(
                $this->db->prefix . self::T_DATASOURCE_VALUE,
                array(
                    self::C_DATASOURCE_VALUE_ID        => Uuid::generate(),
                    self::C_DATASOURCE_VALUE_SOURCE_ID => $dataSourceId,
                    self::C_DATASOURCE_VALUE_LANGUAGE  => $language,
                    self::C_DATASOURCE_VALUE_KEY       => $insertedKey,
                    self::C_DATASOURCE_VALUE_ACTIVE    => (int) $isActive,
                    self::C_DATASOURCE_VALUE_HASH      => md5($hashableString),
                )
            );
        }
    }

    /**
     * Insert new values to an data source.
     *
     * @param string $dataSourceId
     * @param array  $deletedIds
     */
    protected function deleteValues($dataSourceId, array $deletedIds = null)
    {
        if (null === $deletedIds) {
            // delete all
            $this->db->delete(
                $this->db->prefix . self::T_DATASOURCE_VALUE,
                array(
                    self::C_DATASOURCE_VALUE_SOURCE_ID . ' = ?' => $dataSourceId,
                )
            );
        } elseif (count($deletedIds)) {
            $this->db->delete(
                $this->db->prefix . self::T_DATASOURCE_VALUE,
                array(
                    self::C_DATASOURCE_VALUE_ID . ' in (?)'     => $deletedIds,
                    self::C_DATASOURCE_VALUE_SOURCE_ID . ' = ?' => $dataSourceId,
                )
            );
        }
    }

    protected function lowerValueAsKey(array $values)
    {
        $result = array();

        foreach ($values as $value) {
            $result[mb_strtolower($value, 'UTF-8')] = $value;
        }

        return $result;
    }

    protected function lowerValue(array $values)
    {
        foreach ($values as $key => $value) {
            $values[$key] = mb_strtolower($value, 'UTF-8');
        }

        return $values;
    }
}
