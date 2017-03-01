<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\Event;

use Phlexible\Bundle\MetaSetBundle\Entity\MetaDataValue;
use Phlexible\Component\MetaSet\Model\MetaSetFieldInterface;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Meta data value event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaDataValueEvent extends Event
{
    /**
     * @var MetaDataValue
     */
    private $value;

    /**
     * @var MetaSetInterface
     */
    private $metaSet;

    /**
     * @var MetaSetFieldInterface
     */
    private $metaField;

    /**
     * @param MetaDataValue         $value
     * @param MetaSetInterface      $metaSet
     * @param MetaSetFieldInterface $metaField
     */
    public function __construct(MetaDataValue $value, MetaSetInterface $metaSet, MetaSetFieldInterface $metaField)
    {
        $this->value = $value;
        $this->metaSet = $metaSet;
        $this->metaField = $metaField;
    }

    /**
     * @return MetaDataValue
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return MetaSetInterface
     */
    public function getMetaSet()
    {
        return $this->metaSet;
    }

    /**
     * @return MetaSetFieldInterface
     */
    public function getMetaField()
    {
        return $this->metaField;
    }
}
