<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Model;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;

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
     * @param ElementStructure $elementStructure
     */
    public function updateElementStructure(ElementStructure $elementStructure);
}
