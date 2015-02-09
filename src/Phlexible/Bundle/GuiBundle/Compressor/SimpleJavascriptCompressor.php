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
    public function compressString($buffer)
    {
        $buffer = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", "", $buffer);
        $buffer = str_replace(["\r\n","\r","\t","\n",'  ','    ','     '], '', $buffer);
        $buffer = preg_replace(['(( )+\))','(\)( )+)'], ')', $buffer);

        return $buffer;
    }
}
