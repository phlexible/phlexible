<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\Loader;

use Phlexible\Bundle\ElementtypeBundle\Elementtype\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\ElementtypeVersion;

/**
 * Elementtype version loader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * @param Elementtype $elementtype
     * @param int         $version
     *
     * @return ElementtypeVersion
     */
    public function load(Elementtype $elementtype, $version);

    /**
     * @param Elementtype $elementtype
     *
     * @return array
     */
    public function loadVersions(Elementtype $elementtype);

    /**
     * @param ElementtypeVersion $elementtypeVersion
     */
    public function insert(ElementtypeVersion $elementtypeVersion);
}