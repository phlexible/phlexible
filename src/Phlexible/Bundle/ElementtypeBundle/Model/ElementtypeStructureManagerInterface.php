<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Model;

use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeVersion;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element structure manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ElementtypeStructureManagerInterface
{
    /**
     * @param ElementtypeVersion $elementtypeVersion
     *
     * @return ElementtypeStructure
     */
    public function find(ElementtypeVersion $elementtypeVersion);

    /**
     * @param Elementtype $referenceElementtype
     *
     * @return Elementtype[]
     */
    public function findElementtypesUsingReferenceElementtype(Elementtype $referenceElementtype);

    /**
     * @param ElementtypeStructure $elementtypeStructure
     * @param bool                 $flush
     */
    public function updateElementtypeStructure(ElementtypeStructure $elementtypeStructure, $flush = false);

    /**
     * @param ElementtypeStructure $elementtypeStructure
     * @param bool                 $flush
     */
    public function deleteElementtypeStructure(ElementtypeStructure $elementtypeStructure, $flush = true);
}
