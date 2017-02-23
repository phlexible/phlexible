<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Model;

/**
 * Tree node interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TreeNodeInterface
{
    /**
     * @return TreeInterface
     */
    public function getTree();

    /**
     * @param TreeInterface $tree
     *
     * @return $this
     */
    public function setTree(TreeInterface $tree);

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
     * @return TreeNodeInterface
     */
    public function getParentNode();

    /**
     * @param TreeNodeInterface $parentNode
     *
     * @return $this
     */
    public function setParentNode($parentNode);

    /**
     * @return bool
     */
    public function isRoot();

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
    public function getTypeId();

    /**
     * @param string $typeId
     *
     * @return $this
     */
    public function setTypeId($typeId);

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
     * @return array
     */
    public function getAttribute($key, $default = null);

    /**
     * @param string $key
     * @param string $value
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
     * @return int
     */
    public function getSort();

    /**
     * @param int $sort
     *
     * @return $this
     */
    public function setSort($sort);

    /**
     * @return string
     */
    public function getSortMode();

    /**
     * @param string $sortMode
     *
     * @return $this
     */
    public function setSortMode($sortMode);

    /**
     * @return string
     */
    public function getSortDir();

    /**
     * @param string $sortDir
     *
     * @return $this
     */
    public function setSortDir($sortDir);

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
     * @param string $createUid
     *
     * @return $this
     */
    public function setCreateUserId($createUid);

    /**
     * @return bool
     */
    public function getInNavigation();

    /**
     * @param bool $inNavigation
     *
     * @return $this
     */
    public function setInNavigation($inNavigation);

    /**
     * @return array
     */
    public function getCache();

    /**
     * @param array $cache
     *
     * @return $this
     */
    public function setCache($cache);

    /**
     * @return string
     */
    public function getController();

    /**
     * @param string $controller
     *
     * @return $this
     */
    public function setController($controller);

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template);

    /**
     * @return array
     */
    public function getRoutes();

    /**
     * @param array $routes
     *
     * @return $this
     */
    public function setRoutes(array $routes);

    /**
     * @return bool
     */
    public function getNeedAuthentication();

    /**
     * @param bool $needsAuthentication
     *
     * @return $this
     */
    public function setNeedAuthentication($needsAuthentication);

    /**
     * @return string
     */
    public function getSecurityExpression();
}
