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
 * Ensure trailing separator filter
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class EnsureTrailingSeparatorContentFilter implements ContentFilterInterface
{
    /**
     * @var string
     */
    private $separator;

    /**
     * @param string $separator
     */
    public function __construct($separator)
    {
        $this->separator = $separator;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($content)
    {
        return rtrim($content, $this->separator).$this->separator;
    }
}
