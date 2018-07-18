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

/**
 * File interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface FileVersionInterface
{
    /**
     * @return FileInterface
     */
    public function getFile();

    /**
     * @param $file FileInterface
     *
     * @return $this
     */
    public function setFile(FileInterface $file);

    /**
     * @return string
     */
    public function getFileId();

    /**
     * @return int
     */
    public function getVersion();

    /**
     * @param int $fileVersion
     *
     * @return $this
     */
    public function setFileVersion($fileVersion);

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
    public function getMimeType();

    /**
     * @param string $mimeType
     *
     * @return $this
     */
    public function setMimeType($mimeType);

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
     * @return int
     */
    public function getSize();

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setSize($size);

    /**
     * @return string
     */
    public function getHash();

    /**
     * @param string $hash
     *
     * @return $this
     */
    public function setHash($hash);

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
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt);

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
     * @param \DateTime $modifiedAt
     *
     * @return $this
     */
    public function setModifiedAt(\DateTime $modifiedAt);

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
}
