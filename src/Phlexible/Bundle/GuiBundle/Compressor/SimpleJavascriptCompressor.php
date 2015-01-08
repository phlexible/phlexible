<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Compressor;

/**
 * Simple javascript compressor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SimpleJavascriptCompressor extends AbstractStringCompressor
{
    /**
     * {@inheritdoc}
     */
    public function compressString($string)
    {
        return $string;
    }
}
