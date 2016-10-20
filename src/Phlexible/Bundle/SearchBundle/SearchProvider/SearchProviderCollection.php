<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SearchBundle\SearchProvider;

/**
 * Search provider collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SearchProviderCollection implements \IteratorAggregate
{
    /**
     * @var SearchProviderInterface[]
     */
    private $searchProviders = array();

    /**
     * @param SearchProviderInterface[] $searchProviders
     */
    public function __construct(array $searchProviders = array())
    {
        foreach ($searchProviders as $searchProvider) {
            $this->addSearchProvider($searchProvider);
        }
    }

    /**
     * @param SearchProviderInterface $searchProvider
     *
     * @return $this
     */
    public function addSearchProvider(SearchProviderInterface $searchProvider)
    {
        $this->searchProviders[] = $searchProvider;

        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->searchProviders);
    }
}
