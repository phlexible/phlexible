<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
