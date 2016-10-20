<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Compressor;

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
        //$buffer = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", "", $buffer);
        //$buffer = str_replace(["\r\n","\r","\t","\n",'  ','    ','     '], '', $buffer);
        //$buffer = preg_replace(['(( )+\))','(\)( )+)'], ')', $buffer);

        return $buffer;
    }
}
