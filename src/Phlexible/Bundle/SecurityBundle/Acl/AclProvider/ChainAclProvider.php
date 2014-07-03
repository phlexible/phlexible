<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\Acl\AclProvider;

/**
 * Chain acl provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ChainAclProvider implements AclProviderInterface
{
    /**
     * @var AclProviderInterface[]
     */
    private $providers;

    /**
     * @param AclProviderInterface[] $providers
     */
    public function __construct(array $providers = array())
    {
        foreach ($providers as $provider) {
            $this->addAclProvider($provider);
        }
    }

    /**
     * @param AclProviderInterface $provider
     *
     * @return $this
     */
    public function addAclProvider(AclProviderInterface $provider)
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function provideRoles()
    {
        $roles = array();
        foreach ($this->providers as $provider) {
            $roles = array_merge($roles, $provider->provideRoles());
        }

        return $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function provideResources()
    {
        $resources = array();
        foreach ($this->providers as $provider) {
            $resources = array_merge($resources, $provider->provideResources());
        }

        return $resources;
    }

    /**
     * {@inheritdoc}
     */
    public function provideAllow()
    {
        $allow = array();
        foreach ($this->providers as $provider) {
            $allow = array_merge($allow, $provider->provideAllow());
        }

        return $allow;
    }

    /**
     * {@inheritdoc}
     */
    public function provideDeny()
    {
        $deny = array();
        foreach ($this->providers as $provider) {
            $deny = array_merge($deny, $provider->provideDeny());
        }

        return $deny;
    }
}
