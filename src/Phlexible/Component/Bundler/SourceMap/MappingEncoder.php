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
 * Encode mappings
 *
 * @author bspot
 */
class MappingEncoder
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
     * @param Mapping[] $mappings
     *
     * @return array
     */
    public function encode(array $mappings)
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
