<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Model;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;

/**
 * Element structure manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ElementStructureManagerInterface
{
    /**
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return ElementStructure
     */
    public function find(ElementVersion $elementVersion, $language);

    /**
     * @return int
     */
    public function getNextStructureId();

    /**
     * @return int
     */
    public function getNextStructureValueId();

    /**
     * @param ElementStructure $elementStructure
     * @param bool             $onlyValues
     */
    public function updateElementStructure(ElementStructure $elementStructure, $onlyValues);
}
