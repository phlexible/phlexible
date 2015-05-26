<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\File\Dumper;

use FluentDOM\Document;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;

/**
 * Dumper interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DumperInterface
{
    /**
     * @param Elementtype $elementtype
     *
     * @return Document
     */
    public function dump(Elementtype $elementtype);
}
