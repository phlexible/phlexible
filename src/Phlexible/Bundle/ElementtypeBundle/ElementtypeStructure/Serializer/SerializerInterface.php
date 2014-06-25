<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\Serializer;

use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\ElementtypeStructure;

/**
 * Serializer interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SerializerInterface
{
    /**
     * Serialize structure
     *
     * @param ElementtypeStructure $elementtypeStructure
     *
     * @return string
     */
    public function serialize(ElementtypeStructure $elementtypeStructure);
}