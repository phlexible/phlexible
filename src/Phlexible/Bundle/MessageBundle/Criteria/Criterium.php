<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Criteria;

/**
 * Message criterium
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Criterium
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $type
     * @param string $value
     */
    public function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'type'  => $this->getType(),
            'value' => $this->getValue()
        );
    }
}
