<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Content;

/**
 * Mapped content
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MappedContent
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $map;

    /**
     * @param string $content
     * @param string $map
     */
    public function __construct($content, $map)
    {
        $this->content = $content;
        $this->map = $map;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getMap()
    {
        return $this->map;
    }
}
