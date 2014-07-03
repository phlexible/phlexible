<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Checkbox field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CheckboxField extends AbstractField
{
    protected $hasOptions = true;
    protected $icon = 'p-elementtype-field_checkbox-icon';

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
        if (!empty($options['default_value'])) {
            $item['default_content'] = $options['default_value'];

            if ($item['data_content'] === null) {
                $item['data_content'] = $options['default_value'];
            }
        }

        $item['translation_key'] = 'checkbox-' . $item['working_title'] . '-' . ($item['data_content'] ? 'on' : 'off');

        return $item;
    }

}