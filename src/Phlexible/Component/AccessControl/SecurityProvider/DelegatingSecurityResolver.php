<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\SecurityProvider;

/**
 * Delegating security resolver
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
