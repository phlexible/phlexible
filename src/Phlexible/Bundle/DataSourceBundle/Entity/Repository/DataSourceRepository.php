<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\DataSource;

use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;

/**
 * Data source repository
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class DataSourceRepository extends EntityRepository
{
    /**
     * Return all data source titles
     *
     * @param boolean $sorted
     *
     * @return array
     */
    public function getAllDataSourceTitles($sorted = false)
    {
        $qb = $this->createQueryBuilder('d');
        $qb->select(array('d.id', 'd.title'));

        if ($sorted) {
            $qb->orderBy(array('d.title' => 'ASC'));
        }

        $titles = $qb->getQuery()->getScalarResult();

        return $titles;
    }

    /**
     * Return all data source languages
     *
     * @param string $dataSourceId
     *
     * @return array
     */
    public function getAllDataSourceLanguages($dataSourceId)
    {
        $qb = $this->createQueryBuilder('d');
        $qb
            ->distinct()
            ->select('v.language')
            ->where($qb->expr()->eq('d.id', $qb->expr()->literal($dataSourceId)))
            ->join('PhlexibleDataSourceBundle:DataSourceValue', 'v')
        ;

        $languages = $qb->getQuery()->getScalarResult();

        return $languages;
    }

    /**
     * Return all data source ids
     *
     * @return array
     */
    public function getAllDataSourceIds()
    {
        $qb = $this->createQueryBuilder('d');
        $qb->select('d.id');

        $ids = $qb->getQuery()->getScalarResult();

        return $ids;
    }

    public function getAllValuesByDataSourceId($sourceId, $language, $isActive = null)
    {

    }


    /**
     * Remove deleted values / insert added values.
     *
     * @param string $dataSourceId
     * @param string $language
     * @param array  $activeKeys
     * @param array  $inactiveKeys
     */
    protected function _updateValues($dataSourceId, $language, array $activeKeys, array $inactiveKeys)
    {
        $oldKeys = $this->getAllValuesByDataSourceId($dataSourceId, $language);
        $allKeys = array_merge($activeKeys, $inactiveKeys);

        $oldKeysL = $this->_lowerValue($oldKeys);

        // delete values removed from data source
        $this->_deleteValues(
             $dataSourceId,
                 array_keys(array_diff($oldKeysL, $this->_lowerValue($allKeys)))
        );

        // insert values added to data source
        $this->_insertValues(
             $dataSourceId,
                 $language,
                 array_diff_key($this->_lowerValueAsKey($activeKeys), $this->_lowerValueAsKey($oldKeys)),
                 true
        );

        $this->_insertValues(
             $dataSourceId,
                 $language,
                 array_diff_key($this->_lowerValueAsKey($inactiveKeys), $this->_lowerValueAsKey($oldKeys)),
                 false
        );

        // fix existing values
        $this->_fixValues(
             $dataSourceId,
                 array_keys(array_intersect($oldKeysL, $this->_lowerValue($activeKeys))),
                 true
        );

        $this->_fixValues(
             $dataSourceId,
                 array_keys(array_intersect($oldKeysL, $this->_lowerValue($inactiveKeys))),
                 false
        );

    }

    /**
     * Fix active flag in existing keys.
     *
     * @param string  $dataSourceId
     * @param array   $existingIds
     * @param boolean $isActive
     */
    protected function _fixValues($dataSourceId, array $existingIds, $isActive)
    {
        if (!count($existingIds))
        {
            return;
        }

        $this->db->update(
                 $this->db->prefix . self::T_DATASOURCE_VALUE,
                     array(
                         self::C_DATASOURCE_VALUE_ACTIVE => (integer) $isActive,
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
     * @param string  $dataSourceId
     * @param string  $language
     * @param array   $insertedKeys
     * @param boolean $isActive
     * @throws InvalidArgumentException if language not set correctly
     */
    protected function _insertValues($dataSourceId, $language, array $insertedKeys, $isActive)
    {
        if (2 !== strlen($language))
        {
            $msg = 'Language not set correctly. Found: ' . $language;
            throw new InvalidArgumentException($msg);
        }

        foreach ($insertedKeys as $insertedKey)
        {
            $hashableString = $dataSourceId . $insertedKey . $language;

            $this->db->insert(
                     $this->db->prefix . self::T_DATASOURCE_VALUE,
                         array(
                             self::C_DATASOURCE_VALUE_ID        => Uuid::generate(),
                             self::C_DATASOURCE_VALUE_SOURCE_ID => $dataSourceId,
                             self::C_DATASOURCE_VALUE_LANGUAGE  => $language,
                             self::C_DATASOURCE_VALUE_KEY       => $insertedKey,
                             self::C_DATASOURCE_VALUE_ACTIVE    => (integer) $isActive,
                             self::C_DATASOURCE_VALUE_HASH      => md5($hashableString),
                         )
            );
        }
    }

    /**
     * Insert new values to an data source.
     *
     * @param string $dataSourceId
     * @param array  $deletedIds [Optinal] if null all values are deleted
     */
    protected function _deleteValues($dataSourceId, array $deletedIds = null)
    {
        if (null === $deletedIds)
        {
            // delete all
            $this->db->delete(
                     $this->db->prefix . self::T_DATASOURCE_VALUE,
                         array(
                             self::C_DATASOURCE_VALUE_SOURCE_ID . ' = ?' => $dataSourceId,
                         )
            );
        }
        elseif (count($deletedIds))
        {
            $this->db->delete(
                     $this->db->prefix . self::T_DATASOURCE_VALUE,
                         array(
                             self::C_DATASOURCE_VALUE_ID . ' in (?)'     => $deletedIds,
                             self::C_DATASOURCE_VALUE_SOURCE_ID . ' = ?' => $dataSourceId,
                         )
            );
        }
    }

    protected function _lowerValueAsKey(array $values)
    {
        $result = array();

        foreach ($values as $value)
        {
            $result[mb_strtolower($value, 'UTF-8')] = $value;
        }

        return $result;
    }

    protected function _lowerValue(array $values)
    {
        foreach ($values as $key => $value)
        {
            $values[$key] = mb_strtolower($value, 'UTF-8');
        }

        return $values;
    }
}