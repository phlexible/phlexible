<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Model;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Entity\ElementStructureValue;

/**
 * Element structure manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ElementStructureManagerInterface
{
    /**
     * @param ElementVersion $elementVersion
     * @param string         $defaultLanguage
     *
     * @return ElementStructure
     */
    public function find(ElementVersion $elementVersion, $defaultLanguage = null);

    /**
     * @param string $dsId
     * @param string $defaultLanguage
     *
     * @return ElementStructureValue[]
     */
    public function findValues($dsId, $defaultLanguage = null);

    /**
     * @param ElementStructure $elementStructure
     * @param bool             $flush
     */
    public function updateElementStructure(ElementStructure $elementStructure, $flush = true);
}
