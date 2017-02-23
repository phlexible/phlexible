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

use Phlexible\Bundle\SearchBundle\Search\SearchResult;

/**
 * Search provider interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SearchProviderInterface
{
    /**
     * Return role needed for this search.
     *
     * @return string
     */
    public function getRole();

    /**
     * Return search key for this search.
     *
     * @return string
     */
    public function getSearchKey();

    /**
     * Perform search.
     *
     * @param string $query
     *
     * @return SearchResult[]
     */
    public function search($query);
}
