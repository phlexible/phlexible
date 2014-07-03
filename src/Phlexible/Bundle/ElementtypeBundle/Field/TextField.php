<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Text field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TextField extends AbstractField
{
    protected $icon = 'p-elementtype-field_text-icon';

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
        if (strlen($options['default_value'])) {
            $item['default_content'] = $options['default_value'];

            if ($item['data_content'] === null) {
                $item['data_content'] = $options['default_value'];
            }
        }

        return $item;
    }
}