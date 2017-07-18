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

/**
 * Delegating object identity resolver.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingObjectIdentityResolver implements ObjectIdentityResolverInterface
{
    /**
     * @var ObjectIdentityResolverInterface[]
     */
    private $resolvers = array();

    /**
     * @param ObjectIdentityResolverInterface[] $resolvers
     */
    public function __construct(array $resolvers = array())
    {
        foreach ($resolvers as $resolver) {
            $this->addResolver($resolver);
        }
    }

    /**
     * @param ObjectIdentityResolverInterface $resolver
     */
    public function addResolver(ObjectIdentityResolverInterface $resolver)
    {
        $this->resolvers[] = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($objectType, $objectId)
    {
        foreach ($this->resolvers as $resolver) {
            $identity = $resolver->resolve($objectType, $objectId);
            if ($identity) {
                return $identity;
            }
        }

        return null;
    }
}
