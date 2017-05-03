<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\Model;

/**
 * Template interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TemplateInterface
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
    public function getKey();

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key);

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
     * @return bool
     */
    public function getCache();

    /**
     * @param bool $cache
     *
     * @return $this
     */
    public function setCache($cache = true);

    /**
     * @return bool
     */
    public function getSystem();

    /**
     * @param bool $system
     *
     * @return $this
     */
    public function setSystem($system = true);

    /**
     * @return bool
     */
    public function getManaged();

    /**
     * @param bool $managed
     *
     * @return $this
     */
    public function setManaged($managed = true);

    /**
     * @return string
     */
    public function getStorage();

    /**
     * @param string $storage
     *
     * @return $this
     */
    public function setStorage($storage);

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
     * @return array
     */
    public function getParameters();

    /**
     * @param array $parameters
     * @param bool  $strict
     *
     * @return $this
     */
    public function setParameters(array $parameters, $strict = true);

    /**
     * @param string $key
     * @param string $value
     * @param bool   $strict
     *
     * @return $this
     */
    public function setParameter($key, $value, $strict = true);

    /**
     * @param string $key
     * @param string $defaultValue
     *
     * @return string
     */
    public function getParameter($key, $defaultValue = null);

    /**
     * @param string $key
     * @param bool   $notEmpty
     *
     * @return bool
     */
    public function hasParameter($key, $notEmpty = false);

    /**
     * @return array
     */
    public function getAllowedParameters();

    /**
     * @return array
     */
    public function getDefaultParameters();
}
