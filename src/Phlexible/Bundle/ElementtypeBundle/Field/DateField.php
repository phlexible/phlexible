<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Date field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DateField extends AbstractField
{
    protected $icon = 'p-elementtype-field_date-icon';

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

        if (!empty($item['data_content'])) {
            $item['data_content'] =
                substr($item['data_content'], 0, 4) . '-' .
                substr($item['data_content'], 5, 2) . '-' .
                substr($item['data_content'], 8, 2);
            /*.' '.
                            substr($item['data_content'], 8, 2).':'.
                            substr($item['data_content'], 10, 2).':'.
                            substr($item['data_content'], 12, 2);*/
        }


        return $item;
    }
}