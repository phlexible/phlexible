<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\File\Writer;

use Phlexible\Component\Elementtype\Model\Elementtype;

/**
 * Writer interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface WriterInterface
{
    /**
     * @param Elementtype $elementtype
     *
     * @return string
     */
    public function write(Elementtype $elementtype);
}
