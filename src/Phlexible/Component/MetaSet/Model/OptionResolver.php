<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\Model;

/**
 * Option resolver.
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
