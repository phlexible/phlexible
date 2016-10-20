<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\SourceMap;

/**
 * Source map index
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SourceMapIndex
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $content;

    /**
     * SourceMapIndex constructor.
     *
     * @param string $source
     * @param string $content
     */
    public function __construct($source, $content)
    {
        $this->source = $source;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param int $index
     * @param int $destLine
     *
     * @return Mapping[]
     */
    public function getMappings($index, $destLine)
    {
        $mappings = array();

        $lines = substr_count(rtrim($this->content, PHP_EOL).PHP_EOL, PHP_EOL);
        for ($i = 0; $i < $lines; $i++) {
            $mappings[] = new Mapping($destLine + $i, 0, $index, $i, 0);
        }

        return $mappings;
    }
}
