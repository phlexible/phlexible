<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume\Model;

use Phlexible\Component\Volume\VolumeInterface;

/**
 * Folder interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface FolderInterface
{
    /**
     * @return VolumeInterface
     */
    public function getVolume();

    /**
     * @param VolumeInterface $volume
     *
     * @return $this
     */
    public function setVolume(VolumeInterface $volume);

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
    public function getParentId();

    /**
     * @param string $parentId
     *
     * @return $this
     */
    public function setParentId($parentId);

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
    public function getPath();

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path);

    /**
     * @return string
     */
    public function getPhysicalPath();

    /**
     * @param string $physicalPath
     *
     * @return $this
     */
    public function setPhysicalPath($physicalPath);

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes);

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getAttribute($key, $default = null);

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute($key, $value);

    /**
     * @param string $key
     *
     * @return $this
     */
    public function removeAttribute($key);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createTime
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createTime);

    /**
     * @return string
     */
    public function getCreateUserId();

    /**
     * @param string $createUserId
     *
     * @return $this
     */
    public function setCreateUserId($createUserId);

    /**
     * @return \DateTime
     */
    public function getModifiedAt();

    /**
     * @param \DateTime $modifyTime
     *
     * @return $this
     */
    public function setModifiedAt(\DateTime $modifyTime);

    /**
     * @return string
     */
    public function getModifyUserId();

    /**
     * @param string $modifyUserId
     *
     * @return $this
     */
    public function setModifyUserId($modifyUserId);

    /**
     * @return bool
     */
    public function isRoot();
}
