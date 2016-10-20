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

/**
 * LoaderResolverInterface selects a loader for a given resource.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderResolverInterface
{
    /**
     * Returns a loader able to load the resource.
     *
     * @param string $file
     *
     * @return LoaderInterface|false The loader or false if none is able to load the resource
     */
    public function resolve($file);
}
