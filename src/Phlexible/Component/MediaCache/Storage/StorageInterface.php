<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Storage;

use Phlexible\Component\MediaCache\Domain\CacheItem;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;

/**
 * Storage interface.
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
     * @param ExtendedFileInterface $file
     * @param string                $baseUrl
     *
     * @return array
     */
    public function getUrls(ExtendedFileInterface $file, $baseUrl);

    /**
     * @param ExtendedFileInterface $file
     * @param CacheItem             $cacheItem
     * @param string                $baseUrl
     *
     * @return array
     */
    public function getCacheUrls(ExtendedFileInterface $file, CacheItem $cacheItem, $baseUrl);

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
