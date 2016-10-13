<?php

namespace Phlexible\Bundle\GuiBundle\Asset\SourceMap;

class Mapping
{
    /**
     * @var int|null
     */
    private $destLine;

    /**
     * @var int|null
     */
    private $destCol;

    /**
     * @var int|null
     */
    private $srcIndex;

    /**
     * @var int|null
     */
    private $srcLine;

    /**
     * @var int|null
     */
    private $srcCol;

    /**
     * Mapping constructor.
     *
     * @param int $destLine
     * @param int $destCol
     * @param int $srcIndex
     * @param int $srcLine
     * @param int $srcCol
     */
    public function __construct($destLine, $destCol, $srcIndex, $srcLine, $srcCol)
    {
        if (!is_int($destLine) || $destLine < 0) {
            throw new \InvalidArgumentException("destLine has to be 0 or higher");
        }
        if (!is_int($destCol) || $destCol < 0) {
            throw new \InvalidArgumentException("destCol has to be 0 or higher");
        }
        if (!is_int($srcIndex) || $srcIndex < 0) {
            throw new \InvalidArgumentException("srcIndex has to be 0 or higher");
        }
        if (!is_int($srcLine) || $srcLine < 0) {
            throw new \InvalidArgumentException("srcLine has to be 0 or higher");
        }
        if (!is_int($srcCol) || $srcCol < 0) {
            throw new \InvalidArgumentException("srcCol has to be 0 or higher");
        }

        $this->destLine = (int) $destLine;
        $this->destCol = (int) $destCol;
        $this->srcIndex = (int) $srcIndex;
        $this->srcLine = (int) $srcLine;
        $this->srcCol = (int) $srcCol;
    }

    /**
     * @return int|null
     */
    public function getDestLine()
    {
        return $this->destLine;
    }

    /**
     * @return int|null
     */
    public function getDestCol()
    {
        return $this->destCol;
    }

    /**
     * @return int|null
     */
    public function getSrcIndex()
    {
        return $this->srcIndex;
    }

    /**
     * @return int|null
     */
    public function getSrcLine()
    {
        return $this->srcLine;
    }

    /**
     * @return int|null
     */
    public function getSrcCol()
    {
        return $this->srcCol;
    }
}
