<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\MetaData;

/**
 * Meta data interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MetaDataInterface
{
    /**
     * @return array
     */
    public function getIdentifiers();

    /**
     * @param array $identifiers
     *
     * @return $this
     */
    public function setIdentifiers(array $identifiers);

    /**
     * @param string $field
     * @param string $language
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function get($field, $language = null);

    /**
     * @param string $field
     * @param string $value
     * @param string $language
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function set($field, $value, $language = null);

    /**
     * @param string $field
     * @param string $language
     *
     * @return boolean
     */
    public function has($field, $language = null);

    /**
     * @return array
     */
    public function getValues();
}