<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\File\Dumper;

use FluentDOM\Document;
use Phlexible\Component\Elementtype\Model\Elementtype;

/**
 * Dumper interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DumperInterface
{
    /**
     * @param \Phlexible\Component\Elementtype\Model\Elementtype $elementtype
     *
     * @return Document
     */
    public function dump(Elementtype $elementtype);
}
