<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume\FileSource;

/**
 * File source interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface FileSourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @return int
     */
    public function getSize();
}
