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
 * Folder interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface FolderInterface
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
