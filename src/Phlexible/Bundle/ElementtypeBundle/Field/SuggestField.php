<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Suggest field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SuggestField extends AbstractField
{
    protected $hasOptions = true;
    protected $icon       = 'p-elementtype-field_select-icon';

    /**
     * Transform item values
     *
     * @param array $item
     * @param array $media
     * @param array $options
     *
     * @return array
     */
    protected function _transform(array $item, array $media, array $options)
    {
        $item['rawContent'] = $item['data_content'];

        $data = array();
#        if (!empty($options['source_source']))
#        {
#            $dataSourceRepository = MWF_Registry::getContainer()->dataSourcesRepository;
#
#            $sourceId = $options['source_source'];
#            $item['source_id'] = $sourceId;
#            $source = $dataSourceRepository->getDataSourceById($sourceId, $this->_language);
#
#            foreach ($source->getKeys() as $key)
#            {
#                $data[] = array($key, $key);
#            }
#        }
        $item['options'] = $data;

        return $item;
    }

    public function transformSave($value, $eid, $version, $language)
    {
        if (is_array($value))
        {
            $delimiter = MWF_Registry::getContainer()->getParam(':phlexible_elementtype.field.suggest_seperator');
            $value = implode($delimiter, $value);
        }

        return $value;
    }

    public function postSave($value, $structureNode, $eid, $version, $language)
    {
        $dataSourceId = $structureNode->getOptionsValue('source_source');
        $dataSourcesRepository = MWF_Registry::getContainer()->get('datasources.repository');

        $dataSource     = $dataSourcesRepository->getDataSourceById($dataSourceId, $language);
        $dataSourceKeys = $dataSource->getKeys();

        $delimiter           = MWF_Registry::getContainer()->getParam(':phlexible_elementtype.field.suggest_seperator');
        $dataSourceValues    = explode($delimiter, $value);

        $newDataSourceValues = array_diff($dataSourceValues, $dataSourceKeys);
        if (count($newDataSourceValues))
        {
            foreach ($newDataSourceValues as $newDataSourceValue)
            {
                $dataSource->addKey($newDataSourceValue, true);
            }
            $dataSourcesRepository->save($dataSource, MWF_Env::getUid());
        }
    }

    /**
     * Get a translation key to fetch translated content for the frontend.
     *
     * @param $wokingTitle string
     * @param $rawContent  string
     *
     * @return string
     */
    public static function getTranslationKey($wokingTitle, $rawContent)
    {
        return 'select-' . $wokingTitle . '-' . $rawContent;
    }

}
