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
 * Delegating security resolver.
 *
 * @author Marco Fischer <mf@brainbits.net>
 */
class DelegatingSecurityResolver implements SecurityResolverInterface
{
    /**
     * @var SecurityResolverInterface[]
     */
    private $resolvers = array();

    /**
     * @param SecurityResolverInterface[] $resolvers
     */
    public function __construct(array $resolvers = array())
    {
        foreach ($resolvers as $resolver) {
            $this->addResolver($resolver);
        }
    }

    /**
     * @param SecurityResolverInterface $resolver
     */
    public function addResolver(SecurityResolverInterface $resolver)
    {
        $this->resolvers[] = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveName($securityType, $securityId)
    {
        foreach ($this->resolvers as $resolver) {
            $name = $resolver->resolveName($securityType, $securityId);
            if ($name) {
                return $name;
            }
        }

        return null;
    }
}
