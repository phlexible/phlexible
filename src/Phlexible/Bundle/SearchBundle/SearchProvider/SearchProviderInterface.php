<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SearchBundle\SearchProvider;

use Phlexible\Bundle\SearchBundle\Search\SearchResult;

/**
 * Search provider interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SearchProviderInterface
{
    /**
     * Return role needed for this search
     *
     * @return string
     */
    public function getRole();

    /**
     * Return search key for this search
     *
     * @return string
     */
    public function getSearchKey();

    /**
     * Perform search
     *
     * @param string $query
     *
     * @return SearchResult[]
     */
    public function search($query);
}
