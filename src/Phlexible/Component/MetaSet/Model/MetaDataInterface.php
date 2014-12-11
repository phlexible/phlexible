<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\Model;

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
     * @return MetaSet
     */
    public function getMetaSet();

    /**
     * @param MetaSet $metaSet
     *
     * @return $this
     */
    public function setMetaSet($metaSet);

    /**
     * @return array
     */
    public function getLanguages();

    /**
     * @param string $field
     * @param string $language
     *
     * @return string
     */
    public function get($field, $language = null);

    /**
     * @param string $field
     * @param string $value
     * @param string $language
     *
     * @return $this
     */
    public function set($field, $value, $language = null);

    /**
     * @param string $field
     * @param string $language
     *
     * @return bool
     */
    public function has($field, $language = null);

    /**
     * @return array
     */
    public function getValues();
}
