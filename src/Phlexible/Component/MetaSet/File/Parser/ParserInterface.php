<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\File\Parser;

use Phlexible\Component\MetaSet\Model\MetaSetInterface;

/**
 * Parser interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ParserInterface
{
    /**
     * @param string $content
     *
     * @return MetaSetInterface
     */
    public function parse($content);
}
