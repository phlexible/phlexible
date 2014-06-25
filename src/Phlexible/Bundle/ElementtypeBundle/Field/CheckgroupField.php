<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Checkgroup field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CheckgroupField extends AbstractField
{
    protected $hasOptions = true;
    protected $icon       = 'p-elementtype-field_checkgroup-icon';

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
        if(!empty($options['source_list']))
        {
            $interfaceLang = MWF_Env::getUser()->getInterfaceLanguage();

            foreach($options['source_list'] as $values)
            {
                $data[] = array($values['key'], $values[$interfaceLang]);
            }

            $item['options'] = $data;
        }

        return $item;
    }
}