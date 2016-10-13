<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\SourceMap;

/**
 * Asset builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SourceMapBuilder
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var int
     */
    private $line;

    /**
     * @var SourceMapIndex[]
     */
    private $indexes = array();

    /**
     * @param string $file
     * @param int    $line
     */
    public function __construct($file, $line)
    {
        $this->file = $file;
        $this->line = $line;
        $this->index = 0;
    }

    /**
     * @param string $source
     * @param string $content
     *
     * @return SourceMapIndex
     */
    public function add($source, $content)
    {
        return $this->indexes[$this->index++] = new SourceMapIndex($source, $content);
    }

    /**
     * @return array
     */
    private function createSources()
    {
        return array_map(function(SourceMapIndex $index) {return $index->getSource();}, $this->indexes);
    }

    /**
     * @return array
     */
    private function createContents()
    {
        return array_map(function(SourceMapIndex $index) {return $index->getContent();}, $this->indexes);
    }

    /**
     * @return string
     */
    private function createMapping()
    {
        $encoder = new MappingEncoder();

        $mappings = array();
        $line = $this->line;
        foreach ($this->indexes as $i => $index) {
            $indexMappings = $index->getMappings($i, $line);
            $mappings = array_merge($mappings, $indexMappings);
            $line += count($indexMappings);
        }

        return $encoder->encode($mappings);
    }

    /**
     * @return SourceMap
     */
    public function getSourceMap()
    {
        $sourceMap = new SourceMap($this->file, null, $this->createSources(), $this->createContents(), null, $this->createMapping());

        return $sourceMap;
    }
}
