<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Model;

use Phlexible\Bundle\MessageBundle\Entity\Filter;

/**
 * Filter manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface FilterManagerInterface
{
    /**
     * @return Filter
     */
    public function create();

    /**
     * @param string $id
     *
     * @return Filter
     */
    public function find($id);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int   $limit
     * @param int   $offset
     *
     * @return Filter[]
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return Filter
     */
    public function findOneBy(array $criteria, $orderBy = null);

    /**
     * @param Filter $filter
     */
    public function updateFilter(Filter $filter);

    /**
     * @param Filter $filter
     */
    public function deleteFilter(Filter $filter);
}
