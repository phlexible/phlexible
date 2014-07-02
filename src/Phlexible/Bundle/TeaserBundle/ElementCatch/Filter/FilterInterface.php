<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\ElementCatch\Filter;

/**
 * Filter interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface FilterInterface
{
    /**
     * Option name: Name of form field
     */
    const OPTION_NAME = 'name';

    /**
     * Option name: Selected value
     */
    const OPTION_SELECTED = 'selected';

    /**
     * Option name: Valid values
     */
    const OPTION_VALUES   = 'values';

    /**
     * Option name: Valid values for dependent fields
     */
    const OPTION_DEPENDENT = 'dependent';

    /**
     * Constructor.
     *
     * @param Makeweb_Teasers_Catch $catch
     */
    public function __construct(Makeweb_Teasers_Catch $catch);

    /**
     * Get all available values for filter selects.
     *
     * array(
     *     'year' => array(
     *         'name' => 'Jahr',
     *         'values' => array(
     *             '2007' => '2007',
     *             '2008' => '2008',
     *             '2009' => '2009',
     *         )
     *     ),
     *     'quarter' => array(
     *         'name' => 'Quartal',
     *         'sected' => '1',
     *         'values' => array(
     *             '1' => '1. Quartal',
     *             '2' => '2. Quartal',
     *             '3' => '3. Quartal',
     *             '4' => '4. Quartal',
     *         )
     *     )
     * ))
     */
    public function getFilterOptions();

    /**
     * Add filter to a select statement.
     *
     * @param \Zend_Db_Select $select
     * @param bool            $all
     */
    public function filterSelect(\Zend_Db_Select $select, $all = false);

    /**
     * Function to apply some postprocesing to the result.
     *
     * @param array &$result
     */
    public function filterResult(array &$result);

    /**
     * Returns true if filter is used in this request.
     *
     * @return bool
     */
    public function isActive();
}
