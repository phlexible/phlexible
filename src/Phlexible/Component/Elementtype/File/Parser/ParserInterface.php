<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\File\Parser;

use FluentDOM\Document;
use Phlexible\Component\Elementtype\Model\Elementtype;

/**
 * Parser interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ParserInterface
{
    /**
     * @param string $xml
     *
     * @return \Phlexible\Component\Elementtype\Model\Elementtype
     */
    public function parseString($xml);

    /**
     * @param Document $dom
     *
     * @return Elementtype
     */
    public function parse(Document $dom);
}
