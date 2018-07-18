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
use Phlexible\Component\MediaCache\Exception\InvalidArgumentException;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Local storage.
 *
 * @author Peter Fahsel <pfahsel@brainbits.net>
 * @author Thomas Heine <th@brainbits.net>
 */
class LocalStorage extends AbstractStorage
{
    /**
     * @var string
     */
    private $storageDir;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @param CacheManagerInterface $cacheManager
     * @param string                $storageDir
     *
     * @throws InvalidArgumentException
     */
    public function __construct(CacheManagerInterface $cacheManager, $storageDir)
    {
        $this->cacheManager = $cacheManager;
        $this->storageDir = rtrim($storageDir, '/').'/';
    }

    /**
     * {@inheritdoc}
     */
    public function store(CacheItem $cacheItem, $filename)
    {
        $cachePath = $this->storageDir.substr($cacheItem->getId(), 0, 3).'/'.substr($cacheItem->getId(), 3, 3).'/';
        $cacheFilename = $cachePath.$cacheItem->getId().'.'.$cacheItem->getExtension();

        $filesystem = new Filesystem();
        $filesystem->mkdir($cachePath, 0777);
        $filesystem->remove($cacheFilename);
        $filesystem->rename($filename, $cacheFilename);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheUrls(ExtendedFileInterface $file, CacheItem $cacheItem, $baseUrl)
    {
        $fileName = $file->getName();
        $cacheId = $cacheItem->getId();
        $cacheFileName = rawurlencode($this->replaceExtension($fileName, $cacheItem->getExtension()));
        $iconFileName = rawurlencode($this->replaceExtension($fileName, '.gif'));

        $urls = [
            self::MEDIA_PATH_DOWNLOAD => $baseUrl.'/'.self::MEDIA_PATH_DOWNLOAD.'/'.$cacheId.'/'.$cacheFileName,
            self::MEDIA_PATH_MEDIA => $baseUrl.'/'.self::MEDIA_PATH_MEDIA.'/'.$cacheId.'/'.$cacheFileName,
            self::MEDIA_PATH_ICON => $baseUrl.'/'.self::MEDIA_PATH_ICON.'/'.$cacheId.'/16/'.$iconFileName,

            //@todo remove, only in here for frontentmediamanager field image template compatibility reasons
            self::MEDIA_PATH_IMAGE => $baseUrl.'/'.self::MEDIA_PATH_MEDIA.'/'.$cacheId.'/'.$cacheFileName,
        ];

        return $urls;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalPath(CacheItem $cacheItem)
    {
        return sprintf(
            '%s/%s/%s/%s.%s',
            rtrim($this->storageDir, '/'),
            substr($cacheItem->getId(), 0, 3),
            substr($cacheItem->getId(), 3, 3),
            $cacheItem->getId(),
            $cacheItem->getExtension()
        );
    }

    /**
     * @return string
     */
    public function getStorageDir()
    {
        return $this->storageDir;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByFileId($fileId, $fileName)
    {
        $filesystem = new Filesystem();

        foreach ($this->cacheManager->findByFile($fileId) as $cacheFile) {
            $filesystem->remove($cacheFile->getFilePath());
        }
    }
}
