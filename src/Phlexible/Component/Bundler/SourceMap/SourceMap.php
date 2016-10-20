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
 * Source map
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class SourceMap
{
    /**
     * @var int
     */
    public $version = 3;

    /**
     * @var string
     */
    public $file = '';

    /**
     * @var string
     */
    public $sourceRoot = '';

    /**
     * @var array
     */
    public $sources = array();

    /**
     * @var array
     */
    public $sourcesContent = array();

    /**
     * @var array
     */
    public $names = array();

    /**
     * @var string
     */
    public $mappings = '';

    /**
     * SourceMap constructor.
     *
     * @param string|null $file
     * @param string|null $sourceRoot
     * @param array|null  $sources
     * @param array|null  $sourcesContent
     * @param array|null  $names
     * @param string|null $mappings
     */
    public function __construct($file = null, $sourceRoot = null, array $sources = null, array $sourcesContent = null, array $names = null, $mappings = null)
    {
        if ($file !== null) {
            $this->file = $file;
        }
        if ($sourceRoot !== null) {
            $this->sourceRoot = $sourceRoot;
        }
        if ($sources !== null) {
            $this->sources = $sources;
        }
        if ($sourcesContent !== null) {
            $this->sourcesContent = $sourcesContent;
        }
        if ($names !== null) {
            $this->names = $names;
        }
        if ($mappings !== null) {
            $this->mappings = $mappings;
        }
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode(array(
            "version" => $this->version,
            "file" => $this->file,
            "sourceRoot" => $this->sourceRoot,
            "sources" => $this->sources,
            "sourcesContent" => $this->sourcesContent,
            "names" => $this->names,
            "mappings" => $this->mappings
        ));
    }
}
