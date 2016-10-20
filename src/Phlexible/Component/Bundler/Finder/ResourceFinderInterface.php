<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Finder;

use Puli\Repository\Resource\FileResource;

/**
 * Resource finder interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ResourceFinderInterface
{
    /**
     * @param string $typeName
     *
     * @return FileResource[]
     */
    public function findByType($typeName);
}
