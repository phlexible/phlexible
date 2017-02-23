<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SearchBundle\Search;

use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderCollection;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Search.
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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param SearchProviderCollection      $searchProviders
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        SearchProviderCollection $searchProviders,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->searchProviders = $searchProviders;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Query.
     *
     * @param string $query
     *
     * @return SearchResult[]
     */
    public function search($query)
    {
        $searchProviders = [];

        foreach ($this->searchProviders as $searchProvider) {
            if (!$searchProvider instanceof SearchProviderInterface) {
                continue;
            }

            $role = $searchProvider->getRole();
            if ($role && !$this->authorizationChecker->isGranted($role)) {
                continue;
            }

            $searchProviders[] = $searchProvider;
        }

        $explodedQuery = explode(':', $query);
        if (count($explodedQuery) > 1) {
            $keySearchProviders = [];
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

        $results = [];
        foreach ($searchProviders as $searchProvider) {
            $searchResults = $searchProvider->search($query);
            foreach ($searchResults as $searchResult) {
                $results[] = $searchResult->toArray();
            }
        }

        return $results;
    }
}
