<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume\HashCalculator;

use Phlexible\Component\Volume\FileSource\FileSourceInterface;
use Phlexible\Component\Volume\Model\FileInterface;

/**
 * Hash calculator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface HashCalculatorInterface
{
    /**
     * @param FileInterface $file
     *
     * @return string
     */
    public function fromFile(FileInterface $file);

    /**
     * @param FileSourceInterface $fileSource
     *
     * @return string
     */
    public function fromFileSource(FileSourceInterface $fileSource);

    /**
     * @param string $path
     *
     * @return string
     */
    public function fromPath($path);

    /**
     * @param string $string
     *
     * @return string
     */
    public function fromString($string);
}
