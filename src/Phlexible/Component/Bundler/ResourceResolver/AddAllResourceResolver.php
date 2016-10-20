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

/**
 * Add all resource resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AddAllResourceResolver implements ResourceResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve(array $resources)
    {
        return new ResolvedResources($resources, array());
    }
}
