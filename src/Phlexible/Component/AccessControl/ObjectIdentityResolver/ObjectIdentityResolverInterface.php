<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\ObjectIdentityResolver;

use Phlexible\Component\AccessControl\Model\ObjectIdentityInterface;

/**
 * Object resolver interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ObjectIdentityResolverInterface
{
    /**
     * Return object identity from type and id.
     *
     * @param string $objectType
     * @param string $objectId
     *
     * @return ObjectIdentityInterface
     */
    public function resolve($objectType, $objectId);
}
