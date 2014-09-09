<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Filter;

use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;

/**
 * Select filter interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SelectFilterInterface
{
    /**
     * Add filter to a select statement.
     *
     * @param ElementFinderConfig $catch
     * @param \Zend_Db_Select $select
     */
    public function filterSelect(ElementFinderConfig $catch, \Zend_Db_Select $select);

    /**
     * @param array $items
     * @param array $values
     *
     * @return mixed
     */
    public function filterItems(array $items, array $values = array());

    /**
     * Returns true if filter is used in this request.
     *
     * @return bool
     */
    public function isActive();
}
