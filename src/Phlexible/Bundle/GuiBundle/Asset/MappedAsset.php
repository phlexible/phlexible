<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset;

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
