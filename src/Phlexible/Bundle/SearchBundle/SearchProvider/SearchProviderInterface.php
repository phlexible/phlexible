<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SearchBundle\SearchProvider;

/**
 * Search provider interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SearchProviderInterface
{
    /**
     * Return resource needed for this search
     *
     * @return string
     */
    public function getResource();

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
     * @return SearchResult[]
     */
    public function search($query);
}
