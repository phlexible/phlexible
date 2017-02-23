<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaType\Loader;

use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Media type loader interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * Returns whether this class supports the given resource.
     *
     * @param string $file
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($file);

    /**
     * @param string $filename
     *
     * @return MediaType
     */
    public function load($filename);
}
