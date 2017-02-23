<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion;

use Phlexible\Bundle\ElementBundle\Model\ElementStructure;

/**
 * Field mapper interface.
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
interface FieldMapperInterface
{
    /**
     * @param string $key
     *
     * @return bool
     */
    public function accept($key);

    /**
     * @param ElementStructure $elementStructure
     * @param string           $language
     * @param array            $mapping
     *
     * @return string|null
     */
    public function map(ElementStructure $elementStructure, $language, array $mapping);
}
