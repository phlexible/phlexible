<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Model;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;

/**
 * Cache manager interface
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
     * @param array    $criteria
     * @param int|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return mixed
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

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
     * @param CacheItem $cacheItem
     */
    public function updateCacheItem(CacheItem $cacheItem);

    /**
     * @param CacheItem $cacheItem
     */
    public function deleteCacheItem(CacheItem $cacheItem);
}
