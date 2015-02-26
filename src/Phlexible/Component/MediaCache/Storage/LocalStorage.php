<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\Storage;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaCache\Exception\InvalidArgumentException;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Local storage
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
     * @param array                 $options
     * @param CacheManagerInterface $cacheManager
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $options, CacheManagerInterface $cacheManager)
    {
        if (!isset($options['storage_dir'])) {
            throw new InvalidArgumentException('No storage_dir.');
        }

        $this->storageDir = $options['storage_dir'];
        $this->cacheManager = $cacheManager;
    }

    /**
     * {@inheritdoc}
     */
    public function store(CacheItem $cacheItem, $filename)
    {
        $cachePath = $this->storageDir . substr($cacheItem->getId(), 0, 3) . '/' . substr($cacheItem->getId(), 3, 3) . '/';
        $cacheFilename = $cachePath . $cacheItem->getId() . '.' . $cacheItem->getExtension();

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
        $fileName      = $file->getName();
        $cacheId       = $cacheItem->getId();
        $cacheFileName = rawurlencode($this->replaceExtension($fileName, $cacheItem->getExtension()));
        $iconFileName  = rawurlencode($this->replaceExtension($fileName, '.gif'));

        $urls = [
            self::MEDIA_PATH_DOWNLOAD => $baseUrl . '/' . self::MEDIA_PATH_DOWNLOAD . '/' . $cacheId . '/' . $cacheFileName,
            self::MEDIA_PATH_MEDIA    => $baseUrl . '/' . self::MEDIA_PATH_MEDIA . '/' . $cacheId . '/' . $cacheFileName,
            self::MEDIA_PATH_ICON     => $baseUrl . '/' . self::MEDIA_PATH_ICON . '/' . $cacheId . '/16/' . $iconFileName,

            //@todo remove, only in here for frontentmediamanager field image template compatibility reasons
            self::MEDIA_PATH_IMAGE    => $baseUrl . '/' . self::MEDIA_PATH_MEDIA . '/' . $cacheId . '/' . $cacheFileName,
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
