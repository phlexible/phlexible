<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * @return MetaSetInterface
     */
    public function getMetaSet();

    /**
     * @param MetaSetInterface $metaSet
     *
     * @return $this
     */
    public function setMetaSet(MetaSetInterface $metaSet);

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
