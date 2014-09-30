<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\SelectFieldProvider;

/**
 * Select field provider collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SelectFieldProviderCollection
{
    /**
     * @var SelectFieldProviderInterface[]
     */
    private $providers = array();

    /**
     * @param SelectFieldProviderInterface[] $providers
     */
    public function __construct(array $providers = array())
    {
        foreach ($providers as $provider) {
            $this->add($provider);
        }
    }

    /**
     * @param SelectFieldProviderInterface $provider
     *
     * @return $this
     */
    public function add(SelectFieldProviderInterface $provider)
    {
        $this->providers[$provider->getName()] = $provider;
    }

    /**
     * @param string $name
     *
     * @return SelectFieldProviderInterface
     */
    public function get($name)
    {
        return $this->providers[$name];
    }

    /**
     * @return SelectFieldProviderInterface[]
     */
    public function all()
    {
        return $this->providers;
    }
}