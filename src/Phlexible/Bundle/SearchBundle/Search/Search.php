<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SearchBundle\Search;

use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderCollection;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Search
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Search
{
    /**
     * @var SearchProviderCollection
     */
    private $searchProviders;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param SearchProviderCollection $searchProviders
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(SearchProviderCollection $searchProviders, SecurityContextInterface $securityContext)
    {
        $this->searchProviders = $searchProviders;
        $this->securityContext = $securityContext;
    }

    /**
     * Query
     *
     * @param string $query
     *
     * @return SearchResult[]
     */
    public function search($query)
    {
        $searchProviders = array();

        foreach ($this->searchProviders as $searchProvider) {
            if (!$searchProvider instanceof SearchProviderInterface) {
                continue;
            }

            $resource = $searchProvider->getResource();
            if ($resource && !$this->securityContext->isGranted($resource)) {
                continue;
            }

            $searchProviders[] = $searchProvider;
        }

        $explodedQuery = explode(':', $query);
        if (count($explodedQuery) > 1) {
            $keySearchProviders = array();
            $keyWord = strtolower(array_shift($explodedQuery));

            foreach ($searchProviders as $searchProvider) {
                $searchKey = $searchProvider->getSearchKey();
                if ($searchKey === $keyWord) {
                    $keySearchProviders[] = $searchProvider;
                }
            }

            if (count($keySearchProviders)) {
                $searchProviders = $keySearchProviders;
                $query = trim(implode(':', $explodedQuery));
            }
        }

        $results = array();
        foreach ($searchProviders as $searchProvider) {
            $searchResults = $searchProvider->search($query);
            foreach ($searchResults as $searchResult) {
                $results[] = $searchResult->toArray();
            }
        }

        return $results;
    }
}
