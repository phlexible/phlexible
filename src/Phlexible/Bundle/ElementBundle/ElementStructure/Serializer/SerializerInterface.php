<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ElementStructure\Serializer;

use Phlexible\Bundle\ElementBundle\Model\ElementStructure;

/**
 * Serializer interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SerializerInterface
{
    /**
     * Serialize structure.
     *
     * @param ElementStructure $elementStructure
     * @param string           $language
     * @param string           $masterLanguage
     *
     * @return string
     */
    public function serialize(ElementStructure $elementStructure, $language, $masterLanguage);
}
