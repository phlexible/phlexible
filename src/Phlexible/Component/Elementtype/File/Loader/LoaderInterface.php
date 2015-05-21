<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\File\Loader;

use Phlexible\Component\Elementtype\Model\Elementtype;

/**
 * Loader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * @param string $elementtypeId
     *
     * @return \Phlexible\Component\Elementtype\Model\Elementtype
     */
    public function load($elementtypeId);

    /**
     * @return \Phlexible\Component\Elementtype\Model\Elementtype[]
     */
    public function loadAll();
}
