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
 * Meta set field interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MetaSetFieldInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getOptions();

    /**
     * @param string $options
     *
     * @return $this
     */
    public function setOptions($options);

    /**
     * @return array
     */
    public function isRequired();

    /**
     * @param bool $required
     *
     * @return $this
     */
    public function setRequired($required = true);

    /**
     * @return array
     */
    public function isReadonly();

    /**
     * @param bool $readonly
     *
     * @return $this
     */
    public function setReadonly($readonly = true);

    /**
     * @return array
     */
    public function isSynchronized();

    /**
     * @param bool $synchronized
     *
     * @return $this
     */
    public function setSynchronized($synchronized = true);

    /**
     * @return MetaSet
     */
    public function getMetaSet();

    /**
     * @param MetaSetInterface $metaSet
     *
     * @return $this
     */
    public function setMetaSet(MetaSetInterface $metaSet = null);
}
