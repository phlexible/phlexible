<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\File\Parser;

use FluentDOM\Document;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;

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
     * @return Elementtype
     */
    public function parseString($xml);

    /**
     * @param Document $dom
     *
     * @return Elementtype
     */
    public function parse(Document $dom);
}
