<?php

namespace Phlexible\Bundle\GuiBundle\Asset\SourceMap;

/**
 * Generate source maps
 *
 * @author bspot
 */
class SourceMapGenerator
{
    /**
     * @var Base64VLQ
     */
    private $encoder;

    public function __construct()
    {
        $this->encoder = new Base64VLQ();
    }

    /**
     * @param string    $file
     * @param array     $sources
     * @param Mapping[] $mappings
     *
     * @return SourceMap
     */
    public function generate($file, $sourceRoot, array $sources, array $sourcesContent, array $names, array $mappings)
    {
        $map = new SourceMap($file, $sourceRoot, $sources, $sourcesContent, $names, $this->generateMappings($mappings));
        $map->file = $file;
        $map->sources = $sources;
        $map->sourcesContent = $sourcesContent;
        $map->mappings = $this->generateMappings($mappings);

        return $map;
    }

    /**
     * @param Mapping[] $mappings
     *
     * @return array
     */
    private function generateMappings(array $mappings)
    {
        // Group mappings by dest line number.
        $groupedMap = array();
        foreach ($mappings as $mapping) {
            $groupedMap[$mapping->getDestLine()][] = $mapping;
        }

        ksort($groupedMap);

        $groupedMapEnc = array();

        $lastDestLine = 0;
        $lastSrcIndex = 0;
        $lastSrcLine = 0;
        $lastSrcCol = 0;
        foreach ($groupedMap as $destLine => $lineMappings) {
            while (++$lastDestLine < $destLine) {
                $groupedMapEnc[] = ";";
            }

            $lineMapEnc = array();
            $lastDestCol = 0;

            foreach ($lineMappings as $mapping) {
                /* @var $mapping Mapping */
                $mEnc = $this->encoder->encode($mapping->getDestCol() - $lastDestCol);
                $lastDestCol = $mapping->getDestCol();
                if ($mapping->getSrcIndex() !== null) {
                    $mEnc .= $this->encoder->encode($mapping->getSrcIndex() - $lastSrcIndex);
                    $lastSrcIndex = $mapping->getSrcIndex();

                    $mEnc .= $this->encoder->encode($mapping->getSrcLine() - $lastSrcLine);
                    $lastSrcLine = $mapping->getSrcLine();

                    $mEnc .= $this->encoder->encode($mapping->getSrcCol() - $lastSrcCol);
                    $lastSrcCol = $mapping->getSrcCol();
                }
                $lineMapEnc[] = $mEnc;
            }

            $groupedMapEnc[] = implode(",", $lineMapEnc) . ";";
        }

        $groupedMapEnc = implode($groupedMapEnc);

        return $groupedMapEnc;
    }
};
