<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\File\Loader;

use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;

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
     * @return Elementtype
     */
    public function load($elementtypeId);

    /**
     * @param string $elementtypeId
     *
     * @return mixed
     */
    public function open($elementtypeId);

    /**
     * @return Elementtype[]
     */
    public function loadAll();
}
