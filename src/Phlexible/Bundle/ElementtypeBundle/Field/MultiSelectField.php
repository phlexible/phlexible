<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Multi select field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MultiSelectField extends AbstractField
{
    protected $hasOptions = true;
    protected $icon = 'p-elementtype-field_select-icon';

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
        $item['options'] = array();
        $item['translation_key'] = '';

        if (!empty($options['default_value'])) {
            $item['default_content'] = $options['default_value'];
        }

        if (mb_strlen($item['rawContent'])) {
            $values = explode(',', $item['rawContent']);
            $item['content'] = $values;

            $item['translation_keys'] = array();
            foreach ($values as $value) {
                $item['translation_keys'][$value] = 'multiselect-' . $item['working_title'] . '-' . $value;
            }
        }

        if (!empty($options['source_list'])) {
            $item['options'] = $options['source_list'];
        } elseif (!empty($options['source_function'])) {
            $function = $options['source_function'];
            $item['component_function'] = $function;
        }

        return $item;
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