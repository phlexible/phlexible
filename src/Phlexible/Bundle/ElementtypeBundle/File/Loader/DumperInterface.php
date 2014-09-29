<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\File\Loader;

use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;

/**
 * Loader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DumperInterface
{
    /**
     * @param Elementtype $elementtype
     */
    public function dump(Elementtype $elementtype);
}
