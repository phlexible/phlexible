<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Util;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaSetField;
use Phlexible\Bundle\MetaSetBundle\Model\MetaDataInterface;
use Phlexible\Bundle\MetaSetBundle\Model\MetaDataManagerInterface;
use Phlexible\Bundle\MetaSetBundle\Model\MetaSetManagerInterface;

/**
 * Utility class for suggest fields.
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class SuggestFieldUtil
{
    /**
     * @var MetaSetManagerInterface
     */
    private $metaSetManager;

    /**
     * @var MetaDataManagerInterface
     */
    private $metaDataManager;

    /**
     * @var string
     */
    private $seperatorChar;

    /**
     * @param MetaSetManagerInterface  $metaSetManager
     * @param MetaDataManagerInterface $metaDataManager
     * @param string                   $seperatorChar
     */
    public function __construct(MetaSetManagerInterface $metaSetManager, MetaDataManagerInterface $metaDataManager, $seperatorChar)
    {
        $this->metaSetManager = $metaSetManager;
        $this->metaDataManager = $metaDataManager;
        $this->seperatorChar = $seperatorChar;
    }

    /**
     * Fetch all data source values used in any media file metaset.
     *
     * @param DataSourceValueBag $values
     *
     * @return array
     */
    public function fetchUsedValues(DataSourceValueBag $valueBag)
    {
        $metaSets = $this->metaSetManager->findAll();

        $fields = array();
        foreach ($metaSets as $metaSet) {
            foreach ($metaSet->getFields() as $field) {
                if ($field->getOptions() === $valueBag->getDatasource()->getId()) {
                    $fields[] = $field;
                }
            }
        }

        $values = array();
        foreach ($fields as $field) {
            /* @var $field MetaSetField */
            foreach ($this->metaDataManager->findByMetaSet($field->getMetaSet()) as $metaData) {
                /* @var $metaData MetaDataInterface */
                $value = $metaData->get($field->getId(), $valueBag->getLanguage());

                $values[] = $value;
            }
        }

        $values = $this->splitSuggestValues($values);

        return $values;

        $valueSelects = array();
        foreach ($languages as $language) {
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
        foreach ($concatenated as $value) {
            $splitted = explode($this->seperatorChar, $value);
            foreach ($splitted as $key) {
                $key = trim($key);

                // skip empty values
                if (strlen($key)) {
                    $keys[] = $key;
                }
            }
        }

        $uniqueKeys = array_unique($keys);

        return $uniqueKeys;
    }
}
