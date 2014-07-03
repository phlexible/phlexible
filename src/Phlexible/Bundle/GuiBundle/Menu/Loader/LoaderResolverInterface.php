<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Menu\Loader;

/**
 * Loader resolver interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderResolverInterface
{
    /**
     * Resolve loader for file
     *
     * @param string $file
     *
     * @return LoaderInterface
     */
    public function resolve($file);
}