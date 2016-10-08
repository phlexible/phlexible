<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\ResourceResolver;

use Puli\Repository\Resource\FileResource;

/**
 * Resource resolver interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ResourceResolverInterface
{
    /**
     * @param FileResource[] $resources
     *
     * @return ResolvedResources
     */
    public function resolve(array $resources);
}
