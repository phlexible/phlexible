<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle;

/**
 * Meta item interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ItemInterface
{
    /**
     * Return table name
     *
     * @return string
     */
    public function getTableName();

    /**
     * Return identifiers
     *
     * @return array
     */
    public function getIdentifiers();

    /**
     * Return key field
     *
     * @return string
     */
    public function getKeyField();

    /**
     * Return value field
     *
     * @return string
     */
    public function getValueField();
}