<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Util;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Bundle\DataSourceBundle\GarbageCollector\ValuesCollection;
use Phlexible\Component\MetaSet\Model\MetaSetField;
use Phlexible\Component\MetaSet\Model\MetaDataInterface;
use Phlexible\Component\MetaSet\Model\MetaDataManagerInterface;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * Utility class for suggest fields.
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class MediaMetaSuggestFieldUtil implements Util
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
    private $separatorChar;

    /**
     * @param MetaSetManagerInterface  $metaSetManager
     * @param MetaDataManagerInterface $metaDataManager
     * @param string                   $separatorChar
     */
    public function __construct(MetaSetManagerInterface $metaSetManager, MetaDataManagerInterface $metaDataManager, $separatorChar)
    {
        $this->metaSetManager = $metaSetManager;
        $this->metaDataManager = $metaDataManager;
        $this->separatorChar = $separatorChar;
    }

    /**
     * Fetch all data source values used in any media file metaset.
     *
     * @param DataSourceValueBag $valueBag
     *
     * @return ValuesCollection
     */
    public function fetchValues(DataSourceValueBag $valueBag)
    {
        $metaSets = $this->metaSetManager->findAll();

        $fields = [];
        foreach ($metaSets as $metaSet) {
            foreach ($metaSet->getFields() as $field) {
                if ($field->getOptions() === $valueBag->getDatasource()->getId()) {
                    $fields[] = $field;
                }
            }
        }

        $values = new ValuesCollection();

        foreach ($fields as $field) {
            /* @var $field MetaSetField */
            foreach ($this->metaDataManager->findByMetaSet($field->getMetaSet()) as $metaData) {
                /* @var $metaData MetaDataInterface */
                $suggestValues = $this->splitSuggestValue(trim($metaData->get($field->getName(), $valueBag->getLanguage())));

                if (!count($suggestValues)) {
                    continue;
                }

                if ($this->isOnline($metaData)) {
                    $values->addActiveValues($suggestValues);
                } else {
                    $values->addInactiveValues($suggestValues);
                }
            }
        }

        return $values;
    }

    /**
     * @param mixed $metaData
     *
     * @return bool
     */
    private function isOnline($metaData)
    {
        return true;
    }

    /**
     * Split value into parts and remove duplicates.
     *
     * @param string $concatenated
     *
     * @return array
     */
    private function splitSuggestValue($concatenated)
    {
        if (!trim($concatenated)) {
            return array();
        }

        $keys = [];

        $splitted = explode($this->separatorChar, $concatenated);
        foreach ($splitted as $key) {
            $key = trim($key);

            // skip empty values
            if (strlen($key)) {
                $keys[] = $key;
            }
        }

        $uniqueKeys = array_unique($keys);

        return $uniqueKeys;
    }
}
