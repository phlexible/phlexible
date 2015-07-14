<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
