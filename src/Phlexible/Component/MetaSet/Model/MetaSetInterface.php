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

use Phlexible\Component\MetaSet\Domain\MetaSetField;

/**
 * Meta set interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MetaSetInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
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
     * @return int
     */
    public function getRevision();

    /**
     * @param int $revision
     *
     * @return $this
     */
    public function setRevision($revision);

    /**
     * @return MetaSetFieldInterface[]
     */
    public function getFields();

    /**
     * @param MetaSetFieldInterface $field
     *
     * @return $this
     */
    public function addField(MetaSetFieldInterface $field);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasField($name);

    /**
     * @param string $name
     *
     * @return MetaSetField
     */
    public function getField($name);

    /**
     * @param int $id
     *
     * @return MetaSetFieldInterface
     */
    public function getFieldById($id);

    /**
     * @param MetaSetFieldInterface $field
     *
     * @return $this
     */
    public function removeField(MetaSetFieldInterface $field);

    /**
     * @return string
     */
    public function getCreatedBy();

    /**
     * @param string $createUser
     *
     * @return $this
     */
    public function setCreatedBy($createUser);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getModifiedBy();

    /**
     * @param string $modifyUser
     *
     * @return $this
     */
    public function setModifiedBy($modifyUser);

    /**
     * @return \DateTime
     */
    public function getModifiedAt();

    /**
     * @param \DateTime $modifiedAt
     *
     * @return $this
     */
    public function setModifiedAt($modifiedAt);
}
