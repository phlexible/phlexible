<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Model;

use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;

/**
 * File interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface FileInterface
{
    /**
     * @return SiteInterface
     */
    public function getSite();

    /**
     * @param SiteInterface $site
     *
     * @return $this
     */
    public function setSite(SiteInterface $site);

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
     * @return int
     */
    public function getVersion();

    /**
     * @param int $version
     *
     * @return $this
     */
    public function setVersion($version);

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
    public function getFolderId();

    /**
     * @param string $folderId
     *
     * @return $this
     */
    public function setFolderId($folderId);

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
     * @return bool
     */
    public function isHidden();

    /**
     * @param bool $hidden
     *
     * @return $this
     */
    public function setHidden($hidden = true);

    /**
     * @return AttributeBag
     */
    public function getAttributes();

    /**
     * @param AttributeBag $attributes
     *
     * @return $this
     */
    public function setAttributes(AttributeBag $attributes);

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
