<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Model;

/**
 * Template interface
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
     * @return boolean
     */
    public function getCache();

    /**
     * @param boolean $cache
     *
     * @return $this
     */
    public function setCache($cache = true);

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
     * @return integer
     */
    public function getRevision();

    /**
     * @param integer $revision
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
     * @param array   $parameters
     * @param boolean $strict
     *
     * @return $this
     */
    public function setParameters(array $parameters, $strict = true);

    /**
     * @param string  $key
     * @param string  $value
     * @param boolean $strict
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
     * @param string  $key
     * @param boolean $notEmpty
     *
     * @return boolean
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
