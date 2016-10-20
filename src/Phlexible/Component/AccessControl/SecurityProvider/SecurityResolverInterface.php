<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\SecurityProvider;

/**
 * Security resolver interface
 *
 * @author Marco Fischer <mf@brainbits.net>
 */
interface SecurityResolverInterface
{
    /**
     * Return object name
     *
     * @param string $securityType
     * @param string $securityId
     *
     * @return string
     */
    public function resolveName($securityType, $securityId);
}
