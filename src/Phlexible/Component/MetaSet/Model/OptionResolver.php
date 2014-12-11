<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\Model;

/**
 * Option resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class OptionResolver
{
    /**
     * @param MetaSetField $field
     *
     * @return null|array
     */
    public function resolve(MetaSetField $field)
    {
        $type = $field->getType();

        if ($type === 'select') {
            $options = [];
            foreach (explode(',', $field->getOptions()) as $value) {
                $options[] = [$value, $value];
            }

            return $options;
        } elseif ($type === 'suggest') {
            $dataSourceId = $field->getOptions();
            $options = [
                'source_id' => $dataSourceId,
            ];

            return $options;
        }

        return null;
    }

}
