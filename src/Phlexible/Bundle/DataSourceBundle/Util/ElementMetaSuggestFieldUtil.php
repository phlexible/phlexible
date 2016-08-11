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
use Phlexible\Component\MetaSet\Model\MetaDataManagerInterface;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * Utility class for suggest fields.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementMetaSuggestFieldUtil implements Util
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
     * Fetch all data source values used in any element versions.
     *
     * @param DataSourceValueBag $valueBag
     *
     * @return ValuesCollection
     */
    public function fetchValues(DataSourceValueBag $valueBag)
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

        $values = new ValuesCollection();
        foreach ($fields as $field) {
            foreach ($this->metaDataManager->findByMetaSet($field->getMetaSet()) as $metaData) {
                $value = $metaData->get($field->getId(), $valueBag->getLanguage());

                $value = $this->splitSuggestValue($value);
                if ($this->isOnline($metaData)) {
                    $values->addActiveValue($value);
                } else {
                    $values->addInactiveValue($value);
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
