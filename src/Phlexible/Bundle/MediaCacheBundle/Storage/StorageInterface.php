<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Storage;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Storage interface
 *
 * @author Peter Fahsel <pfahsel@brainbits.net>
 */
interface StorageInterface
{
    /**
     * @param CacheItem $cacheItem
     * @param string    $filename
     */
    public function store(CacheItem $cacheItem, $filename);

    /**
     * @param FileInterface $file
     * @param string        $baseUrl
     *
     * @return array
     */
    public function getUrls(FileInterface $file, $baseUrl);

    /**
     * @param FileInterface $file
     * @param CacheItem     $cacheItem
     * @param string        $baseUrl
     *
     * @return array
     */
    public function getCacheUrls(FileInterface $file, CacheItem $cacheItem, $baseUrl);

    /**
     * @param CacheItem $cacheItem
     *
     * @return string
     */
    public function getLocalPath(CacheItem $cacheItem);

    /**
     * @param string $fileId
     * @param string $fileName
     */
    public function deleteByFileId($fileId, $fileName);
}
