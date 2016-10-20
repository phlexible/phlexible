<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\ResourceResolver;

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
