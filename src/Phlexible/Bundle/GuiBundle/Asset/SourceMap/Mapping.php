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
        $this->destLine = $destLine;
        $this->destCol = $destCol;
        $this->srcIndex = $srcIndex;
        $this->srcLine = $srcLine;
        $this->srcCol = $srcCol;
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
