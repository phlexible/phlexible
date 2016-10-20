<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Filter;

/**
 * Content filter interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ContentFilterInterface
{
    /**
     * @param string $content
     *
     * @return string
     */
    public function filter($content);
}
