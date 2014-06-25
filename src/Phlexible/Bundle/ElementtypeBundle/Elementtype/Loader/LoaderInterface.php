<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Elementtype\Loader;

use Phlexible\Bundle\ElementtypeBundle\Elementtype\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Elementtype\ElementtypeCollection;

/**
 * Elementtype loader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * @return ElementtypeCollection
     */
    public function load();

    /**
     * @param Elementtype $elementtype
     */
    public function insert(Elementtype $elementtype);

    /**
     * @param Elementtype $elementtype
     */
    public function update(Elementtype $elementtype);

    /**
     * @param Elementtype $elementtype
     */
    public function delete(Elementtype $elementtype);

    /**
     * @param Elementtype $elementtype
     */
    public function softDelete(Elementtype $elementtype);
}