<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Util;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaSetField;
use Phlexible\Bundle\MetaSetBundle\Model\MetaDataInterface;
use Phlexible\Bundle\MetaSetBundle\Model\MetaDataManagerInterface;
use Phlexible\Bundle\MetaSetBundle\Model\MetaSetManagerInterface;

/**
 * Utility class for suggest fields.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SuggestFieldUtil
{
    /**
     * @var string
     */
    private $seperatorChar;

    /**
     * @param string $seperatorChar
     */
    public function __construct($seperatorChar)
    {
        $this->seperatorChar = $seperatorChar;
    }

    /**
     * Fetch all data source values used in any element versions.
     *
     * @param DataSourceValueBag $valueBag
     *
     * @return array
     */
    public function fetchUsedValues(DataSourceValueBag $valueBag)
    {
        /*
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
            foreach ($this->metaDataManager->findByMetaSet($field->getMetaSet()) as $metaData) {
                $value = $metaData->get($field->getId(), $valueBag->getLanguage());

                $values[] = $value;
            }
        }
        */
        // TODO: aus elementen

        $values = array();

        $values = $this->splitSuggestValues($values);

        return $values;
    }

    /**
     * Split list of suggest values into pieces and remove duplicates.
     *
     * @param array $concatenated
     *
     * @return array
     */
    private function splitSuggestValues(array $concatenated)
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
