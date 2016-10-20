<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Asset;

/**
 * Mapped asset
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MappedAsset extends Asset
{
    /**
     * @var string
     */
    private $mapFile;

    /**
     * @param string $file
     * @param string $mapFile
     */
    public function __construct($file, $mapFile)
    {
        parent::__construct($file);

        $this->mapFile = $mapFile;
    }

    /**
     * @return string
     */
    public function getMapFile()
    {
        return $this->mapFile;
    }
}
