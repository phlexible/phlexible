<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
