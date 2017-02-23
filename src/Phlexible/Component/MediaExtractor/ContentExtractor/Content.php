<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaExtractor\ContentExtractor;

/**
 * Content.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Content
{
    /**
     * @var string
     */
    private $content = '';

    /**
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->content = (string) $content;
    }

    /**
     * Set new content.
     *
     * @param string $content
     */
    public function set($content)
    {
        $this->content = (string) $content;
    }

    /**
     * Get content as string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->content;
    }
}
