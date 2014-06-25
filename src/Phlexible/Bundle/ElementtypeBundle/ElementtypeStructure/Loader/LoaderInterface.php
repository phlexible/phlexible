<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\Loader;

use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\ElementtypeVersion;

/**
 * Elementtype structure loader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * Load structure
     *
     * @param ElementtypeVersion $elementTypeVersion
     * @param string             $refererDsId
     *
     * @return ElementtypeStructure
     */
    public function load(ElementtypeVersion $elementTypeVersion, $refererDsId = null);

    /**
     * @param ElementtypeStructure $elementtypeStructure
     */
    public function insert(ElementtypeStructure $elementtypeStructure);
}