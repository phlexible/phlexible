<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Meta;

class MetaValueAccessor
{
    private $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function __call($method, $parameters)
    {
        if (isset($this->values[$method])) {
            return $this->values[$method];
        }

        return '';
    }
}
