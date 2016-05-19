<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\Worker;

use FFMpeg\FFProbe;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaExtractor\Transmutor;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaCache\CacheIdStrategy\CacheIdStrategyInterface;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaCache\Storage\StorageManager;
use Phlexible\Component\MediaTemplate\Applier\VideoTemplateApplier;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaTemplate\Model\VideoTemplate;
use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Null cache worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NullWorker extends AbstractWorker
{
    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var CacheIdStrategyInterface
     */
    private $cacheIdStrategy;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CacheManagerInterface     $cacheManager
     * @param CacheIdStrategyInterface  $cacheIdStrategy
     * @param LoggerInterface           $logger
     */
    public function __construct(
        CacheManagerInterface $cacheManager,
        CacheIdStrategyInterface $cacheIdStrategy,
        LoggerInterface $logger
    )
    {
        $this->cacheManager = $cacheManager;
        $this->cacheIdStrategy = $cacheIdStrategy;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function process(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        $cacheId = $this->cacheIdStrategy->createCacheId($template, $file);

        $cacheItem = $this->cacheManager->find($cacheId);
        if (!$cacheItem) {
            return null;
        }

        if ($cacheItem->getCacheStatus() !== CacheItem::STATUS_OK && $cacheItem->getCacheStatus() !== CacheItem::STATUS_DELEGATE) {
            $this->cacheManager->deleteCacheItem($cacheItem);

            return null;
        }

        return $cacheItem;
    }
}
