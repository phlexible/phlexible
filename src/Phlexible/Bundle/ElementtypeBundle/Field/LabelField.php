<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Label field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LabelField extends AbstractField
{
    protected $icon = 'p-elementtype-field_label-icon';

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
        $interfaceLang = MWF_Env::getUser()->getInterfaceLanguage();

        if (!$item['data_content'] && !empty($options['text_' . $interfaceLang])) {
            $item['data_content'] = $options['text_' . $interfaceLang];
        }

        return $item;
    }
}