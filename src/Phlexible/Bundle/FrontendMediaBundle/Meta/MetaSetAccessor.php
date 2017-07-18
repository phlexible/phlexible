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

class MetaSetAccessor
{
    private $sets = [];

    public function __construct(array $sets)
    {
        foreach ($sets as $set => $values) {
            $this->sets[$set] = new MetaValueAccessor($values);
        }
    }

    public function __call($method, $parameters)
    {
        if (isset($this->sets[$method])) {
            return $this->sets[$method];
        }

        return new MetaValueAccessor(array());
    }
}
