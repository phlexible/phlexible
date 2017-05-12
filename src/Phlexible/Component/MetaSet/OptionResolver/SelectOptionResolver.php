<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\OptionResolver;

use Phlexible\Component\MetaSet\Domain\MetaSetField;

/**
 * Select option resolver.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SelectOptionResolver implements OptionResolverInterface
{
    /**
     * @param MetaSetField $field
     *
     * @return null|array
     */
    public function resolve(MetaSetField $field)
    {
        $options = [];
        foreach (explode(',', $field->getOptions()) as $value) {
            $options[] = [$value, $value];
        }

        return $options;
    }
}
