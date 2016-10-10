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
 * Resolved resources
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ResolvedResources
{
    /**
     * @var FileResource[]
     */
    private $resources;

    /**
     * @var FileResource[]
     */
    private $unusedResources;

    /**
     * ResolvedResources constructor.
     *
     * @param FileResource[] $resources
     * @param FileResource[] $unusedResources
     */
    public function __construct(array $resources, array $unusedResources = array())
    {
        $this->resources = $resources;
        $this->unusedResources = $unusedResources;
    }

    /**
     * @return FileResource[]
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * @return FileResource[]
     */
    public function getUnusedResources()
    {
        return $this->unusedResources;
    }
}
