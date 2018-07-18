<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Model;

use Phlexible\Component\MediaCache\Domain\CacheItem;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Cache manager interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface CacheManagerInterface
{
    /**
     * @param string $id
     *
     * @return CacheItem
     */
    public function find($id);

    /**
     * @return CacheItem[]
     */
    public function findAll();

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return CacheItem[]
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     *
     * @return CacheItem
     */
    public function findOneBy(array $criteria, $orderBy = null);

    /**
     * @param int      $fileId
     * @param int|null $fileVersion
     *
     * @return CacheItem[]
     */
    public function findByFile($fileId, $fileVersion = null);

    /**
     * @param string $templateKey
     * @param int    $fileId
     * @param int    $fileVersion
     *
     * @return CacheItem
     */
    public function findByTemplateAndFile($templateKey, $fileId, $fileVersion);

    /**
     * @param TemplateInterface $template
     *
     * @return CacheItem[]
     */
    public function findOutdatedTemplates(TemplateInterface $template);

    /**
     * @param array $criteria
     *
     * @return int
     */
    public function countBy(array $criteria);

    /**
     * @return int
     */
    public function countAll();

    /**
     * @param CacheItem $cacheItem
     */
    public function updateCacheItem(CacheItem $cacheItem);

    /**
     * @param CacheItem $cacheItem
     */
    public function deleteCacheItem(CacheItem $cacheItem);
}
